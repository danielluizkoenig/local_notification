define(['jquery', 'core/str', 'core/modal_factory'],
    function ($, Str, ModalFactory) {
    return {
        init: function () {
            $(document).ready(function () {
                $("#id_time").blur(function () {
                    let timeValue = $(this).val();
                    let cleanedValue = timeValue.replace(/\s/g, "");
                    $(this).val(cleanedValue);
                });
                $(".modal-usuarios").click(async function (e) {
                    e.preventDefault();
                    const loadingstudents = await
                     Str.get_string("loadingstudents", "local_notification");
                    const errorfetchingstudents = await
                     Str.get_string("errorfetchingstudents", "local_notification");
                    ModalFactory.create({
                        type: ModalFactory.types.DEFAULT,
                        title: 'Usuários',
                        body: loadingstudents
                    }).then(function(modal) {
                        modal.show();
                        $.ajax({
                            method: "POST",
                            url: $(this).attr("href"),
                            dataType: "html",
                            success: function (response) {
                                modal.setBody(response);
                            },
                            error: function () {
                                modal.setBody(errorfetchingstudents);
                            }
                        });
                    }.bind(this));
                });
            });
        }
    };
});