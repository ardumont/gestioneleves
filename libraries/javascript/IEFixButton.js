/**
 * IEFixButton.
 * This library help to fix button behavior on Internet Explorer 7 and lower.
 *
 * @author Lionel SAURON
 * @version 1.0
 */
var IEFixButton = (function()
{	// =============================================================================
	// || Private members
	// =============================================================================

	/**
	 * Conditional Compilation to test if we need Fix.
	 * Fix for IE 7 and lower.
	 * Other browsers are standards compliant.
	 *
	 * @private
	 * @see http://www.javascriptkit.com/javatutors/conditionalcompile.shtml
	 */
	/*@cc_on
		/*@if(@_jscript_version <= 5.7)
			var bNeedFix = true;
		@else @*/
			var bNeedFix = false;
		/*@end
	@*/

	/**
	 * Create hidden input to store button value on form submit.
	 * Called by navigator.
	 *
	 * @author Lionel SAURON
	 * @version 1.0
	 * @private
	 */
	function onFormSubmit()
	{
		// Special Internet Explorer
		var objEvent = window.event;
		var objForm = objEvent.srcElement;

		// Retrieve button that was clicked
		var objClickedButton = objForm._IEFixButton_submitButton;
	
		// For each button of the form
		var aButtons = objForm.getElementsByTagName('BUTTON');
		for(var i=0; i<aButtons.length; i++)
		{
			var objButton = aButtons[i];
		
			// Send the right value for clicked button
			if(objButton === objClickedButton)
			{
				// Create the hidden input with the name of the button
				// Use value attribute and not button name
				objHiddenInput = document.createElement('input');
				objHiddenInput.type = "hidden";
				objHiddenInput.name = objButton.name;
				objHiddenInput.value = objButton.attributes.getNamedItem("value").nodeValue
			
				objForm.appendChild(objHiddenInput);
				
				// Store hidden input if we need to cancel submit
				objForm._IEFixButton_hiddenInput = objHiddenInput;
			}
	
			// Remove button's name attritut, so no data will be send on form submit
			// Store name for later use (cancel submit)
			objButton._IEFixButton_name = objButton.name;
			objButton.name = "";

		}// END for each button

	}// END function IEFixButton.onFormSubmit
	/**
	 * Store button name to retrieve it on form submit.
	 * Called by navigator.
	 *
	 * @author Lionel SAURON
	 * @version 1.0
	 * @private
	 */
	function onButtonClick()
	{		// Special Internet Explorer
		var objEvent = window.event;
		var objButton = objEvent.srcElement;

		// Store on the form wich button was clicked
		objButton.form._IEFixButton_submitButton = objButton;

	}// END function IEFixButton.onButtonClick

	// =============================================================================
	// || Public members
	// =============================================================================
	return {

	/**
	 * Remove fix operation when submit is cancelled.
	 *
	 * Must be called if you cancel a submit event on a form.
	 *
	 * @author Lionel SAURON
	 * @version 1.0
	 * @private
	 */
	cancelSubmit : function(objForm)
	{
		if(bNeedFix == false) return;

		var IEFix_UndefinedVarTodelete;
		
		// For each button of the form
		var aButtons = objForm.getElementsByTagName('BUTTON');
		for(var i=0; i<aButtons.length; i++)
		{
			var objButton = aButtons[i];

			// Restore button's name attritut
			if(typeof(objButton._IEFixButton_name) != "undefined")
			{
				objButton.name = objButton._IEFixButton_name;
				
				// IE fix for operation "delete objButton._IEFixButton_name"
				objButton._IEFixButton_name = IEFix_UndefinedVarTodelete;
			}
			
		}// END for each button
		
		// Remove the hidden input
		if(typeof(objForm._IEFixButton_hiddenInput) != "undefined")
		{
			objForm.removeChild(objForm._IEFixButton_hiddenInput);
		
			// IE fix for operation "delete objButton._IEFixButton_hiddenInput"
			objForm._IEFixButton_hiddenInput = IEFix_UndefinedVarTodelete;
		}

		// Remove the button that was clicked
		if(typeof(objForm._IEFixButton_submitButton) != "undefined")
		{
			// IE fix for operation "delete objButton._IEFixButton_submitButton"
			objForm._IEFixButton_submitButton = IEFix_UndefinedVarTodelete;
		}

	},// END function IEFixButton.cancelSubmit

	/**
	 * Fix a form.
	 *
	 * @author Lionel SAURON
	 * @version 1.0
	 * @public
	 */
	fixForm: function(objForm)
	{
		if(bNeedFix == false) return;

		// Add submit listener on the form (to send only the good submit value)
		objForm.attachEvent("onsubmit", onFormSubmit);
		
		// For each button of the form
		var aButtons = objForm.getElementsByTagName('BUTTON');
		for(var i=0; i<aButtons.length; i++)
		{
			var objButton = aButtons[i];
		
			// Add click listener on the button (to store with button was clicked)
			objButton.attachEvent("onclick", onButtonClick);
		}// END for each button

		// Form is fix now
		objForm._IEFixButton_fixEnabled = true;

	},// END function IEFixButton.fixForm

	/**
	 * Unfix a form.
	 *
	 * @author Lionel SAURON
	 * @version 1.0
	 * @public
	 */
	unfixForm: function(objForm)
	{
		if(bNeedFix == false) return;

		// Add submit listener on the form (to send only the good submit value)
		objForm.detachEvent("onsubmit", onFormSubmit);
		
		// For each button of the form
		var aButtons = objForm.getElementsByTagName('BUTTON');
		for(var i=0; i<aButtons.length; i++)
		{
			var objButton = aButtons[i];
		
			// Add click listener on the button (to store with button was clicked)
			objButton.detachEvent("onclick", onButtonClick);
		}// END for each button
		
		// Form is unfix now
		objForm._IEFixButton_fixEnabled = false;
		
	},// END function IEFixButton.unfixForm

	/**
	 * Check if form is fix.
	 *
	 * @author Lionel SAURON
	 * @version 1.0
	 * @public
	 */
	isFormFix: function(objForm)
	{
		if(bNeedFix == false) return false;

		var bIsFormFix = false;

		if(typeof(objForm._IEFixButton_fixEnabled) != "undefined")
		{
			bIsFormFix = objForm._IEFixButton_fixEnabled;
		}
		
		return bIsFormFix;
		
	},// END function IEFixButton.isFormFix
	
	/**
	 * Fix all forms.
	 *
	 * @author Lionel SAURON
	 * @version 1.0
	 */
	fixAllForms: function()
	{
		if(bNeedFix == false) return;

		// For each form of the page
		var aForms = document.getElementsByTagName('FORM');
		for(var i=0; i<aForms.length; i++)
		{
			IEFixButton.fixForm(aForms[i]);

		}// END for each form

	},// END function IEFixButton.fixAllForms

	/**
	 * Fix all forms on page load.
	 *
	 * @author Lionel SAURON
	 * @version 1.0
	 */
	fixAllFormsOnLoad: function()
	{
		if(bNeedFix == false) return;

		window.attachEvent("onload", IEFixButton.fixAllForms);

	}// END function IEFixButton.fixAllFormsOnLoad

	};// END public members
})();
