
require(["jquery"], function($){

	    $(document).ready(function() {
	        if (typeof window.avisVerifies === 'undefined') {
	            window.avisVerifies = {};
	        }
	        window.avisVerifies.jQuery = window.jQuery;
	    });

});



   function showNetreviewsTab() {

                 var $avjq = window.avisVerifies.jQuery;
                 var tab=$avjq('#product-review-custom').parent().parent().attr('id');
                 $avjq('.pp-tabs ul.tabs li[rel='+tab+']').trigger('click');
                 $avjq('html, body').animate({scrollTop:eval($avjq('#idTabavisverifies').position().top)}, 'slow');
    
        

        /*var tabs = document.getElementsByClassName("product data items")[0].childNodes; // Tab to show
        for( var i = 0; i < tabs.length; i++ )
        {
            if ( tabs[ i ].nodeType != Node.TEXT_NODE ) { // Esquiva los elementos text_node.
                // Desactivar/ocultar las otras pestañas.
                if( tabs[ i ].hasAttribute("class") && tabs[ i ].hasAttribute("aria-selected") && tabs[ i ].hasAttribute("aria-expanded") ) {
                    tabs[ i ].classList.remove("active");
                    tabs[ i ].setAttribute("aria-selected", "false");
                    tabs[ i ].setAttribute("aria-expanded", "false");
                }
                if( tabs[ i ].hasAttribute("aria-hidden") && tabs[ i ].hasAttribute("style") ) {
                    tabs[ i ].setAttribute("aria-hidden", "true");
                    tabs[ i ].style.display = "none";
                }

            }

            // Ocultar el título de una pestaña ESPECÍFICA.
            /*if ( tabs[ i ].id == "tab-label-product.info.description" || tabs[ i ].id == "tab-label-additional" ) // any attribute could be used here
            {
                tabs[ i ].classList.remove("active");
                tabs[ i ].setAttribute("aria-selected", "false");
                tabs[ i ].setAttribute("aria-expanded", "false");
            }

            // Ocultar el contenido de una pestaña ESPECÍFICA.
            if ( tabs[ i ].id == "product.info.description" || tabs[ i ].id == "additional" ) // any attribute could be used here
            {
                tabs[ i ].setAttribute("aria-hidden", "true");
                tabs[ i ].style.display = "none";
            }*

            // Mostrar el título de la pestaña VR.
            if ( tabs[ i ].id == "tab-label-verified.reviews.tab" )
            {
                tabs[ i ].className += tabs[ i ].className ? ' active' : 'active';
                tabs[ i ].setAttribute("aria-selected", "true");
                tabs[ i ].setAttribute("aria-expanded", "true");
            }

            // Mostrar el contenido de la pestaña VR.
            if ( tabs[ i ].id == "verified.reviews.tab" )
            {
                tabs[ i ].setAttribute("aria-hidden", "false");
                tabs[ i ].style.display = "block";
            }
        }
        var $avjq = window.avisVerifies.jQuery;
        $avjq('html,body').animate({scrollTop: $avjq("#idTabavisverifies").offset().top}, 'slow'); */
    }

    function netReviewsMoreReviews(){
        var $avjq = window.avisVerifies.jQuery;  
        var avisVerifiesAjaxUrl = $avjq("#avisVerifiesAjaxUrl").val();
        var avisVerifiesPageNumber = $avjq("#avisVerifiesPageNumber").val();
        var avisVerifiesReviewsPerPage = $avjq("#avisVerifiesReviewsPerPage").val();
        var avisVerifiesProductSku = $avjq("#avisVerifiesProductSku").val();
        var avisVerifiesProductId = $avjq("#avisVerifiesProductId").val();
        var $content = $avjq("#ajax_comment_content");
        $avjq("#avisVerifiesAjaxImage").css('display','block');

        $avjq.ajax({
            url: avisVerifiesAjaxUrl,
            type: "POST",
            data: {
                'avisVerifiesProductSku' : avisVerifiesProductSku, 
                'avisVerifiesProductId' : avisVerifiesProductId, 
                'avisVerifiesPageNumber' : avisVerifiesPageNumber, 
                'avisVerifiesReviewsPerPage' : avisVerifiesReviewsPerPage
            },
            success: function(data){
                $content.append(data);
                avisVerifiesPageNumber++;
                $avjq("#avisVerifiesPageNumber").val(avisVerifiesPageNumber);
                $avjq("#avisVerifiesAjaxImage").css('display','none');
                if($avjq(".reviewInfosAV").length == $avjq("#avisverifiesNbTotalReviews").val()) {
                    $avjq("#av_load_next_page").hide();
                }
            },
            error: function ( jqXHR, textStatus, errorThrown ){
                console.log('something went wrong...');
                $avjq("#avisVerifiesAjaxImage").css('display','none');
            }
        });
    }