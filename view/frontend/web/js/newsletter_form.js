require(['jquery','Magento_Ui/js/modal/modal', "mage/validation"],function (jQuery,modal) {
    /*************************************************
     * This script manages the modal environment in
     * the frontend store. It creates, opens, and
     * destroys the different modal dialogs shown
     * throughout the newsletter registration.
     */
    var modalObject = '';
    var successModalOpts = {
        type: 'popup',
        responsive: true,
        innerScroll: true,
        autoOpen: true,
        title:'Thank you',
        modalClass:'newsletter_form_modal',
        buttons: [{
            text:"Close",
            action: function () {
                this.closeModal();
            }
        }]
    };
    var failedModalOpts = {
        type: 'popup',
        responsive: true,
        innerScroll: true,
        autoOpen: true,
        title:'Registration Failed',
        modalClass:'newsletter_form_modal',
        buttons: [{
            text:"Close",
            class:'nl_close',
            action: function () {
                this.closeModal();
            }
        }]
    };
    var initModalopts = {
        type: 'popup',
        responsive: true,
        innerScroll: true,
        autoOpen: true,
        title:'Register to the newsletter',
        modalClass:'newsletter_form_modal',
        buttons: [{
            text: 'Register to the newsletter',
            class: 'nl_submit',
            click: function() {
                var mail = jQuery('#nl_email').val();
                jQuery.ajax({
                    url:'nl_new_user',
                    method:'POST',
                    data:{email:mail},
                    success:function(data){
                        if(data['status'] === 'subscriber_saved_confiramtion_required'){
                            modalObject.modal('closeModal');
                            jQuery('.nl_success').modal(successModalOpts);
                        }
                        else if(data['status'] === 'exception'){
                            alert(data['except_message']);
                            modalObject.modal('closeModal');
                            jQuery('.nl_failed').modal(failedModalOpts);
                        }
                        else{
                            modalObject.modal('closeModal');
                            jQuery('.nl_failed').modal(failedModalOpts);
                        }
                    },
                    error:function(data){
                        alert(data);
                        modalObject.modal('closeModal');
                        jQuery('.nl_failed').modal(failedModalOpts);
                    }
                });
            }
        }]
    };
    modalObject = jQuery(".form_container").modal(initModalopts);
});
