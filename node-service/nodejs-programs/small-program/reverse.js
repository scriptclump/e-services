function reverse1(str){

	// Check the input
	if(!str || str.length <3 || typeof str !== "string"){
		return "Please enter the valid parameters";
	}


	rev_str = [];
	for(let i=str.length -1; i>= 0; i--){
		rev_str.push(str[i]);
	}
	return rev_str;
}

function reverse2(str){
	return str.split().reverse().join();
}

const reverse3 = str => str.split().reverse().join();

const reverse4 = str => [...str].reverse().join();
