document.observe("dom:loaded", function() {
    //alert(shippingOriginSubmitUrl);
    $('carriers_interparcel_origin_postcode').insert({        
        after: "<div class='autocomplete' id='interparcel_postcode_autocomplete'></div>"
    });
    
    
    new Ajax.Autocompleter(
        'carriers_interparcel_origin_postcode',
        'interparcel_postcode_autocomplete',
        shippingOriginSubmitUrl,
        {
            paramName : "postcode",
            minChars : 2,            
            updateElement : getSelectionId,
            parameters : 'country='+$('carriers_interparcel_origin_country').value
        }
    );
        
    function getSelectionId(li) {
        $('carriers_interparcel_origin_postcode').value = li.getAttribute('postcode');        
        $('carriers_interparcel_origin_city').value = li.getAttribute('city');        
    }
    
    new Ajax.Autocompleter(
        'city',
        'city_autocomplete',
        shippingOriginSubmitUrl,
        {
            paramName : "postcode",
            minChars : 2,            
            updateElement : updateBycity,
            parameters : 'country='+$('country').value
        }
    );        
    
    function updateBycity(li) {
        $('postcode').value = li.getAttribute('postcode');        
        $('city').value = li.getAttribute('city');        
    }
    
});