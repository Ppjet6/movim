var empty_count = 0;

/**
 * Attach a callback function to an event.
 */
function movimRegisterPollHandler(type, func)
{
	if(!(type in movimPollHandlers)) {
		movimPollHandlers[type] = new Array();
	}
	movimPollHandlers[type].push(func);
}

/**
 * Polls the server.
 */
function movim_poll()
{
	poller = rpc.make_xmlhttp();
	poller.open('GET', 'jajax.php?do=poll', true);

	poller.onreadystatechange = function()
	{
		if(poller.readyState == 4)
		{
			if(poller.status == 200) {
				// Handling poll return.
				if(poller.responseXML == null) {
				    if(empty_count == 3)
				        movim_disconnect();
				    else
				        empty_count++;
				} else
                    rpc.handle_rpc(poller.responseXML);
            } else if(poller.status == 500) {            
                    movim_disconnect();
            }
            
			if(poller.status > 0) {
				// Restarting polling.
				movim_poll();
			}

		}
	};

	poller.send();
}

function movim_disconnect()
{
    var url = window.location.href;
    var urlparts = url.split('/');
    var txt = urlparts[0]+'//';
    for(i = 2; i < urlparts.length-1; i++) {
        txt = txt+urlparts[i]+'/'
    }
    window.location.replace(txt+'index.php?q=disconnect');
}

function halt_poll()
{
	poller.abort();
}

// Adding the polling to onload event.
movim_add_onload(movim_poll);
