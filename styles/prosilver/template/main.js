//------------------------------------------------------------------------------
// General functions
//------------------------------------------------------------------------------
function FullReset()
{
	window.location.reload();
}

//------------------------------------------------------------------------------
// Automatic scroll of the messages log
//------------------------------------------------------------------------------
MessageLog =
{
	LockScroll: true,
	AutoScroll: function()
	{
		if(!this.LockScroll) return;
		var o = $("#main")[0];
		o.scrollTop = o.scrollHeight-o.clientHeight;
	},
	CheckScroll: function()
	{
		var o = $("#main")[0];
		this.LockScroll = (o.scrollTop >= o.scrollHeight - o.clientHeight*1.20);
	},
	Add: function(text)
	{
		$("#main").append("<div>"+text+"</div>");
		this.AutoScroll();
	}
};

jQuery(function($)
{
	$("#main").scroll(function()
	{
		MessageLog.CheckScroll();
	});
	$(window).resize(function()
	{
		MessageLog.AutoScroll();
		MessageLog.CheckScroll();
	});
});

//------------------------------------------------------------------------------
// Message editor
//------------------------------------------------------------------------------
MessageEdit =
{
	Color: "000000",
	AddressTo: "",
	Sended: false,
	SetColor: function(color)
	{
		if (color == null)
		{
			if (localStorage.getItem('ChatColor') !== null)
			{
				this.Color = localStorage.getItem('ChatColor');
			}
		}
		else
		{
			this.Color = color;
		}
		localStorage.setItem('ChatColor', this.Color);
		$("#message").css("color", '#'+this.Color);
	},
	To: function(login, priv)
	{
		var type = (priv==true)?"private":"to";
		var Message = $("#message").val();
		if(Message.indexOf(this.AddressTo)!=0) this.AddressTo = "";
		Message = Message.substring(this.AddressTo.length);
		if(this.Sended)
		{
			this.AddressTo = "";
			this.Sended = false;
		}
		if(this.AddressTo.indexOf(type)!=0) this.AddressTo = "";
		var NewAddr = type+" ["+login+"] ";
		if(this.AddressTo.indexOf(NewAddr)==-1)
		{
			// Add new appeal
			this.AddressTo += NewAddr;
		}
		else
		{
			// Remove existent appeal
			var i = this.AddressTo.indexOf(NewAddr)
			this.AddressTo = this.AddressTo.substring(0, i) + this.AddressTo.substring(i+NewAddr.length);
		}
		$("#message").val(this.AddressTo + Message).focus();
	},
	Smilie: function(s)
	{
		var Message = $("#message").val();
		$("#message").val(Message+" "+s+" ").focus();
	},
	SendClick: function()
	{
		var Message = $("#message").val();
        //document.write(Message);
		if(Message.indexOf(this.AddressTo)!=0) this.AddressTo = "";
		if(Message == "")
		{
			RefreshChat();
		}
		else if(Message == this.AddressTo)
		{
			$("#message").val("");
			this.AddressTo = "";
            SendMessage(Message, this.Color);
            //document.write(this.AddressTo);
		}
		else
		{
			this.Sended = true;
			SendMessage(Message, this.Color);
			$("#message").val(this.AddressTo);
		}
		$("#message").focus();
	}
};
DeleteMessage =
{
	To: function(IDMESD)
	{
    	DelMessage(IDMESD);
	}
};
jQuery(function($)
{
	MessageEdit.SetColor(null); // Load from the settings storage
	$("#message").keypress(function(e)
	{
		if (e.which == 13) MessageEdit.SendClick();
	});
	$("#btn-send").click(function(e)
	{
		MessageEdit.SendClick();
		return false;
	});
});

//------------------------------------------------------------------------------
// Sounds
//------------------------------------------------------------------------------
Sound =
{
	Enabled: 1,
	Enable: function(onoff)
	{
		var a = document.createElement('audio');
		if (!(a.canPlayType && (a.canPlayType('audio/mpeg;').replace(/no/, '') || a.canPlayType('audio/ogg; codecs="vorbis"').replace(/no/, ''))))
		{
			this.Enabled = 0;
		}
		else
		{
			if (onoff == null)
			{
				if (localStorage.getItem('ChatSound') !== null)
				{
					this.Enabled = localStorage.getItem('ChatSound');
				}
			}
			else
			{
				this.Enabled = onoff ? 1 : 0;
			}
			this.Enabled = parseInt(this.Enabled);
			localStorage.setItem('ChatSound', this.Enabled);
		}
		$("#btn-sound").css("background-position", this.Enabled ? 'right bottom' : 'left top');
	},
	Play: function(src)
	{
		if(this.Enabled && $('#audio-'+src).length && $('#audio-'+src)[0].play) $('#audio-'+src)[0].play();
	}
};

jQuery(function($)
{
	Sound.Enable(null); // Load from the settings storage
	$("#btn-sound").click(function(e)
	{
		Sound.Enable(!Sound.Enabled);
		return false;
	});
});

//------------------------------------------------------------------------------
// Output messages log
//------------------------------------------------------------------------------
function addslashes(str)
{
	return str.replace(/\\/g,'\\\\').replace(/\'/g,'\\\'').replace(/\"/g,'\\"').replace(/\0/g,'\\0');
}

function stripslashes(str)
{
	return str.replace(/\\'/g,'\'').replace(/\\"/g,'"').replace(/\\0/g,'\0').replace(/\\\\/g,'\\');
}

function LogEvent(text)
{
	NewMessages = true;
	$("#main").append("<div>"+text+"</div>")
	MessageLog.AutoScroll();
}

function LogMessage(id,time,nick,msg,color)
{
	var html = "";
	if(time) html += "<span class='date'><a href='#' onclick=\"javascript:DeleteMessage.To('"+id+"')\">"+time+"</a></span>&nbsp;";
	if(nick) html += "[<a href='#' onclick=\"javascript:MessageEdit.To('"+addslashes(nick)+"'); return false;\">"+nick+"</a>]&nbsp;";
	if(color) html += "<span style='color:#"+color+"'>"+msg+"</span>"; else html += msg;
	LogEvent(html);
}

function LogUserJoin(id,time,nick)
{
	LogEvent("<span class='date'>"+time+"</span> {L_USER_JOINED} [<a href='#' onclick=\"javascript:MessageEdit.To('"+addslashes(nick)+"'); return false;\">"+nick+"</a>]");
}

function LogUserLeft(id,time,nick)
{
	LogEvent("<span class='date'>"+time+"</span> {L_USER_LEFT} [<a href='#' onclick=\"javascript:MessageEdit.To('"+addslashes(nick)+"'); return false;\">"+nick+"</a>]");
}

function SetUsers(users)
{
	document.getElementById("users").innerHTML="<h1>{L_NOW_IN_CHAT} ("+users.length+")</h1>";
	for(var i = 0; i < users.length; i++)
	{
		var obj = document.createElement('div');
		obj.innerHTML="";
		obj.innerHTML+="&nbsp;<a href='#' onclick=\"javascript:MessageEdit.To('"+addslashes(users[i].name)+"', true); return false;\"><img src='./ext/Sumanai/simplechat/media/private.png' height='10px' width='10px' border='0' title='{L_PRIVATE}' /></a>&nbsp;";
		obj.innerHTML+="<a href='#' onclick=\"javascript:MessageEdit.To('"+addslashes(users[i].name)+"'); return false;\">"+users[i].name+"</a>";
		document.getElementById("users").appendChild(obj);
	}
}

//------------------------------------------------------------------------------
// Refresh
//------------------------------------------------------------------------------
function ShowIcon(name)
{
	$("#icon-error").hide();
	$("#icon-sending").hide();
	$("#icon-loading").hide();
	$("#icon-"+name).show();
}

var InProgress	= false;
var LastUpdate	= 0;
var NewMessages	= false;
var UpdateCount	= 0;

function RefreshChat()
{
	if(InProgress) return;
	InProgress = true;
	ShowIcon("loading");
	var VisiblemesFlag = '';
	if (visiblemes.checked)
  		{  VisiblemesFlag = 'on';}
  	else
  		{  VisiblemesFlag = ''; }

	$.ajax(
	{
		type: 		"POST",
		url: 		"simplechat?build={BUILD_TIME}",
		data: 		{"action": "sync", "lastid": LastUpdate,"visiblemes":VisiblemesFlag},
		dataType:	'script',
		cache:		false,
		timeout:	15000
	})
	.done(function(js)
	{
		ShowIcon("none");
		UpdateCount++;
		$("#upd_counter").text(UpdateCount);
	})
	.fail(function()
	{
		ShowIcon("error");
	})
	.always(function()
	{
		InProgress = false;
	});
}

function SendMessage(text, color)
{
    //document.write(text);
	ShowIcon("sending");
	if(!color) color = "000000";
	$.ajax(
	{
		type: 		"POST",
		url: 		"simplechat?build={BUILD_TIME}",
		data: 		{"action": "say", "text": text, "color": color},
		dataType:	'script',
		cache:		false,
		timeout:	10000
	})
	.done(function(js)
	{
		ShowIcon("none");
		RefreshChat();
	})
	.fail(function()
	{
		ShowIcon("error");
	});
}
function DelSync()
{
	var VisiblemesFlag = '';
	if (visiblemes.checked)
  		{  VisiblemesFlag = 'on';}
  	else
  		{  VisiblemesFlag = ''; }

	$.ajax(
	{
		type: 		"POST",
		url: 		"simplechat?build={BUILD_TIME}",
		data: 		{"action": "sync", "lastid": '',"visiblemes":VisiblemesFlag},
		dataType:	'script',
		cache:		false,
		timeout:	15000
	})	
}
function DelMessage(IDMES)
{
	//LastUpdate=IDMES;
	$.ajax(
	{
		type: 		"POST",
		url: 		"simplechat?build={BUILD_TIME}",
		data: 		{"action": "del", "ID": IDMES},
		dataType:	'script',
		cache:		false,
		timeout:	10000
	})
	DelSync()
	.done(function(js)
	{
		ShowIcon("none");
		RefreshChat();
	})
	.fail(function()
	{
		ShowIcon("error");
	});
}

function SetLastId(lastid)
{
	if(lastid) if(LastUpdate != lastid)
	{
		LastUpdate = lastid;
		if (NewMessages)
		{
			Sound.Play('notify');
		}
		NewMessages = false;
		$("#msg_counter").text(lastid);
	}
}

var ChatDelay = 15;			// Refresh speed
var ChatTimer = -1;			// Refresh timer

function SetDelay(delay)
{
	if (delay == null)
	{
		if (localStorage.getItem('ChatDelay') !== null)
		{
			ChatDelay = localStorage.getItem('ChatDelay');
		}
		document.getElementById("refresh").value = ChatDelay;
	}
	else
	{
		ChatDelay = delay;
	}
	localStorage.setItem('ChatDelay', ChatDelay);
	if (ChatTimer>=0) clearInterval(ChatTimer);
	ChatTimer = setInterval('RefreshChat()', ChatDelay*1000); 
	//ChatTimer = setInterval('DelSync()', ChatDelay*1000); 
}

//------------------------------------------------------------------------------
// Entry point
//------------------------------------------------------------------------------
jQuery(function($)
{
	SetDelay(null);
	RefreshChat();
});

//------------------------------------------------------------------------------
// Output the colors table (C) 23.03.2006 VEG
//------------------------------------------------------------------------------
function GradColors(colmax)
{
	var colhex = ["00", "33", "66", "99", "cc", "ff"];
	var coldat = [colmax, 0, 0];
	var colcur = 2;
	var result = [];
	do
	{
		if((coldat[colcur]!=colmax)&&(coldat[(colcur+1)%3]==colmax)) coldat[colcur]++; else
		if ((coldat[colcur]==colmax)&&(coldat[(colcur+1)%3]!=0)) coldat[(colcur+1)%3]--; else
		{
			colcur--;
			if(colcur>=0) coldat[colcur]++;
		};
		if(colcur>=0) result[result.length]=colhex[coldat[0]]+colhex[coldat[1]]+colhex[coldat[2]];
	} while (colcur!=-1);
	return result;
}

function WriteColorTable(rows, width, height)
{
	document.write("<table border='0' cellspacing='0' cellpadding='0' width='"+width+"px' style='cursor: pointer; border-spacing: 0;'>");
	for(var i=rows; i>=1; i--)
	{
		document.write("<tr><td><table border='0' cellspacing='0' cellpadding='0' width='100%' height='"+height+"px'><tr>");
		var grad = GradColors(i);
		var wcur = 0;
		for(var j=0; j<grad.length; j++)
			document.write("<td style='font-size: 1px; background-color: #"+grad[j]+"' onclick='javascript:MessageEdit.SetColor(\""+grad[j]+"\");'><div style='height: 1px; width: 1px;'></div></td>");
		document.write("</tr></table></td></tr>");
	}
	document.write("</table>");
}
