document.observe("dom:loaded", function() {    
    new Ajax.Autocompleter(
        'city',
        'city_autocomplete',
        shippingOriginSubmitUrl,
        {
            paramName : "city",
            minChars : 2,
            updateElement : updateByCity,
            callback    : function(test){return {city : test.value, country : $('country').value};}
        }
    );
    function updateByCity(li) {
        $('postcode').value = li.getAttribute('postcode');        
        $('city').value = li.getAttribute('city');        
    }

    new Ajax.Autocompleter(
        'postcode',
        'postcode_autocomplete',
        shippingOriginSubmitUrl,
        {
            paramName : "postcode",
            minChars : 2,
            updateElement : updateByCode,
            parameters : 'country='+$('country').value
        }
    );  

    function updateByCode(li) {
        $('postcode').value = li.getAttribute('postcode');        
        $('city').value = li.getAttribute('city');        
    }    
    
    
    //if($('billing:city') && $('billing:postcode')){
        //alert('here');
//        new Ajax.Autocompleter(
//            'billing:city',
//            'autocompleteanchor',
//            shippingOriginSubmitUrl,
//            {
//                paramName : "city",
//                minChars : 2,
//                updateElement : updateByCity,
//                parameters : 'country='+$('billing:country_id').value
//            }
//        );
//        function updateByCity(li) {
//            $('billing:postcode').value = li.getAttribute('postcode');        
//            $('billing:city').value = li.getAttribute('city');        
//        }
        
//        new Ajax.Autocompleter(
//            'billing:postcode',
//            'autocompleteanchor',
//            shippingOriginSubmitUrl,
//            {
//                paramName : "postcode",
//                minChars : 2,
//                updateElement : updateByCode,
//                parameters : 'country='+$('billing:country_id').value
//            }
//        );  
//            
//        function updateByCode(li) {
//            $('billing:postcode').value = li.getAttribute('postcode');        
//            $('billing:city').value = li.getAttribute('city');        
//        }
    //}
    
});