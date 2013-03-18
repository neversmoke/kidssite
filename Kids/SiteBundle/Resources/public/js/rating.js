function initPage()
{
	var rates = document.getElementsByTagName("ul");
	for (i = 0; i < rates.length; i ++)
	{
		if (rates[i].className.indexOf("star-rating-list") != -1)
		{
			rates[i]._lis = rates[i].getElementsByTagName("li");
			
			rates[i].onmouseover = function() {
				for (k = 0; k < this._lis.length; k++)
				{
					if (this._lis[k].className.indexOf("active") != -1)
					{
						this._active = this._lis[k];
						this._lis[k].className = this._lis[k].className.replace("active", "");
					}
					
					if (this._lis[k].className.indexOf("setted") != -1)
					{
						this._setted = this._lis[k];
						this._lis[k].className = this._lis[k].className.replace("setted", "");
					}
				}
			}
			rates[i].onmouseout = function() {
				if (this._active && this._active.className.indexOf("active") == -1 && !this._setted)
				{
					this._active.className += " active";
				}
				
				if (this._setted && this._setted.className.indexOf("setted") == -1)
				{
					this._setted.className += " setted";
				}
			return false;	
			}
			
			var links = rates[i].getElementsByTagName("a");
			for (k = 0; k < links.length; k ++)
			{
				links[k].onclick = function() {
					
					this.parentNode.parentNode._in_hover = true;
					this.parentNode.parentNode._setted = this.parentNode;
					
					if (this.parentNode.className.indexOf("setted") == -1)	
					{
						this.parentNode.className += " setted";
					}
					this.blur();
					return false;
				}
			}
		}
	}
}

if (window.addEventListener)
	window.addEventListener("load", initPage, false);
else if (window.attachEvent)
	window.attachEvent("onload", initPage);