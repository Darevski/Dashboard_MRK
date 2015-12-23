function OnClickTaskChoose(ClickedBlockNumber)
{
	document.getElementById("block-container").style.height = "0px";
    document.getElementById("button-back").style.marginTop = "0px";    
}
function OnClickSetBlocksByDefault()
{
	document.getElementById("block-container").style.height = "515px";	
    document.getElementById("button-back").style.marginTop = "1px";
}
function OnclickShowMultiContainer()
{
	document.getElementById("login-box").style.transitionDelay = "0s";    
	document.getElementById("login-box").style.left = "calc(100% + 100px)";
    document.getElementById("multiuse-container").style.left = "calc(100% - 256px)";
    document.getElementById("multi-close").style.display = "inline-block";
}
function CloseMultiContainer()
{
    document.getElementById("multiuse-container").style.left = "calc(100% + 100px)";
	document.getElementById("login-box").style.transitionDelay = "0.4s";        
	document.getElementById("login-box").style.left = "calc(100% - 50px)";
    document.getElementById("multi-close").style.display = "none";
}