document.observe("dom:loaded", function() {    
    new Ajax.Autocompleter(
        'billing:city',
        'autocomplete_anchor',
        shippingOriginSubmitUrl,
        {
            paramName : "billing:city",
            minChars : 2,
            updateElement : function(li){$('billing:postcode').value = li.getAttribute('postcode');$('billing:city').value = li.getAttribute('city');},
            callback    : function(test){return {city : test.value, country : $('billing:country_id').value};}
        }
    );
    new Ajax.Autocompleter(
        'billing:postcode',
        'autocomplete_anchor',
        shippingOriginSubmitUrl,
        {
            paramName : "billing:postcode",
            minChars : 2,
            updateElement : function(li){$('billing:postcode').value = li.getAttribute('postcode');$('billing:city').value = li.getAttribute('city');},
            callback    : function(test){return {postcode : test.value, country : $('billing:country_id').value};}
        }
    );
//    function updateByCity(li) {
//        $('billing:postcode').value = li.getAttribute('postcode');        
//        $('billing:city').value = li.getAttribute('city');        
//    }

//    new Ajax.Autocompleter(
//        'postcode',
//        'postcode_autocomplete',
//        shippingOriginSubmitUrl,
//        {
//            paramName : "postcode",
//            minChars : 2,
//            updateElement : updateByCode,
//            parameters : 'country='+$('country').value
//        }
//    );  
//
//    function updateByCode(li) {
//        $('postcode').value = li.getAttribute('postcode');        
//        $('city').value = li.getAttribute('city');        
//    }    
    
    
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