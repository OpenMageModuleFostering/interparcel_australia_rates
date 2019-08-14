document.observe("dom:loaded", function() {    
    new Ajax.Autocompleter(
        'shipping:city',
        'autocomplete_anchor',
        shippingOriginSubmitUrl,
        {
            paramName : "shipping:city",
            minChars : 2,
            updateElement : function(li){$('shipping:postcode').value = li.getAttribute('postcode');$('shipping:city').value = li.getAttribute('city');},
            callback    : function(test){return {city : test.value, country : $('shipping:country_id').value};}
        }
    );
    new Ajax.Autocompleter(
        'shipping:postcode',
        'autocomplete_anchor',
        shippingOriginSubmitUrl,
        {
            paramName : "shipping:postcode",
            minChars : 2,
            updateElement : function(li){$('shipping:postcode').value = li.getAttribute('postcode');$('shipping:city').value = li.getAttribute('city');},
            callback    : function(test){return {postcode : test.value, country : $('shipping:country_id').value};}
        }
    );

    
});