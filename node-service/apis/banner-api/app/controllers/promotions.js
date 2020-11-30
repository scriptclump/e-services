const promotionmodel = require('../models/promotionmodel');
module.exports = {
	getSuggestion: function(req,res){
		promotionmodel.getSuggestionInCart(req.body.data).then(result=>{
			//console.log('some',result);	
			res.send(result);
		});
	}
}