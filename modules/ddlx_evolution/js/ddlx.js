/*
	#####################################
	#									#
	#	DDLX Template configuration		#
	#									#
	#	Author : Alan DEDOBBELER		#
	#	Date : 	18/05/2010				#
	#	for DDLX Multimedia				#
	#									#
	#####################################
 */

function popup()
{
	window
			.open(
					'../index.php?test',
					'page de test',
					config = 'height=600, width=500, toolbar=no, menubar=no, scrollbars=yes, resizable=yes, location=no, directories=no, status=no' )
}

function loadTemplate( action, template )
{
	if (action = 'load')
	{
		if (confirm( 'Do you want to load these parameters ?' ))
		{
			document.forms['displayform'].load.value = template;
			document.forms['displayform'].submit();
		}
	}
	if (action = 'display')
	{
		if (confirm( 'Do you want display your template ?' ))
		{
			document.forms['displayform'].display.value = template;
			document.forms['displayform'].submit();
		}
	}
}
/* jQuery.noConflict(); */
/*
 * $(window).bind("load", function() { $("div#slider1").codaSlider(); });
 */
function checkTransparency( checkBox, target )
{
	var t = document.getElementById( target );
	if (checkBox.checked)
		t.value = 'transparent';
}

/* ************ jpicker multiple *********** */


