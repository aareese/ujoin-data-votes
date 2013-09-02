var request = require('request')
    ,config = JSON.parse(require('fs').readFileSync('./config.json', 'utf8'))
	,Mongolian = require('mongolian')
	,db = new Mongolian(config.mongo_uri || process.env.MONGOHQ_URL)
	,async = require('async')
	,rtg = require("url").parse(config.redis_uri || process.env.REDISTOGO_URL)
	,sha1 = require('sha1')
	,bills = db.collection('bills')
    ,redis = require("redis").createClient(rtg.port, rtg.hostname);

var DEFAULT_STATE = config.state,
	DEFAULT_DATE = config.furthest_date;

redis.auth(rtg.auth.split(":")[1]);
module.exports.pag = 1;
function search(pag, callback, query, date, sort, state){
	console.log('searching page ' + pag.toString())
	request({
		url: 'http://openstates.org/api/v1/bills',
		qs: {
			apikey: config.sunlight_api || process.env.SUNLIGHT_API_KEY,
			state: (state || DEFAULT_STATE).toLowerCase(),
			updated_since: date || DEFAULT_DATE,
			per_page: 10,
			page: module.exports.pag || 1,
			sort: sort || 'last',
			q: (config.query || undefined)
		}
	}, function(err, response, _results_){
		var arr = JSON.parse(_results_);
		if (arr.length < 1){
			if (callback) callback(false);
			module.exports.pag = false;
			console.log('search complete');
			module.exports.processor.drain();
			return;
		} 
		async.each(arr, function(bill, callback){
			redis.lpush('queue', bill.id, function(err){
				console.log('queued: ' + bill.title);
				if (callback) callback(err);
			});
		}, function(){
			module.exports.pag += 1;
			if (callback) callback(false);

			module.exports.processor.drain();
		}); 
	})
}

module.exports.search = search;

function processor(bill_id, callback){
	console.log('searching for bill: ' + bill_id)
	request({
		url: 'http://openstates.org/api/v1/bills/' + bill_id + '/',
		qs: {
			apikey: config.sunlight_api || process.env.SUNLIGHT_API_KEY	
		}
	}, function(err, res, body){
		var obj = JSON.parse(body);
		bills.save(obj, function(err){
			console.log('saved: ' + obj.title);
			 callback (err);
		});
	});
}

module.exports.processor = async.queue(processor, 1);
module.exports.processor.drain = function(){
	redis.lpop('queue', function(err, id){
		if (!err && id) {
			bills.findOne({ id: id }, function(err, doc){
				if (!doc) module.exports.processor.push(id);
				else 
				{ 
					console.log('skipped: ' + doc.title);
					module.exports.processor.drain();
				}
				delete doc;
			});
		}
		else {
			console.log('queue empty');
			if (module.exports.pag !== false) 
				search(module.exports.pag);
			else 
				console.log('crawl complete');
		}
	});
}
