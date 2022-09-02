function getContentUrl()
{
	var url= document.getElementById("urlGetContent").value;
	console.log(url);

	if(url!=''){
		import Mercury from "@postlight/mercury-parser";

		Mercury.parse(url).then(result => console.log(result));
	}


}