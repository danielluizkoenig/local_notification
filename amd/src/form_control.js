/* eslint-disable no-console */
define(['jquery', 'core/modal_factory', 'core/modal', 'core/str'], function($, ModalFactory, Modal, Str) {

  const QUERY_ID_VALUE = "5";
  const NO_END_DATE = "0";

  // Função para converter timestamp em data
  let converteTimestamp = function(time) {
      let timestampInMilliseconds = time * 1000;
      let date = new Date(timestampInMilliseconds);
      let day = String(date.getDate()).padStart(2, "0");
      let month = String(date.getMonth() + 1).padStart(2, "0");
      let year = date.getFullYear();
      return day + "/" + month + "/" + year;
  };

  // Função para exibir o modal
  let exibirModalAviso = async function() {
      try {
          const bodyText = await Str.get_string("courseend_empty", "local_notification");
          // Usando ModalFactory como fallback, já que Modal.create falhou
          const modal = await ModalFactory.create({
              type: ModalFactory.types.DEFAULT,
              title: 'Aviso',
              body: bodyText,
              large: false
          });
          modal.show();
          $("#id_notification_query_id").val('');
      } catch (error) {
          console.error('Erro ao criar o modal:', error);
      }
  };

  // Função para verificar o tipo de notificação
  let verificaNottificacaoTipo = function($endcourse) {
      let selectedValue = $("#id_notification_query_id").val();
      $("#id_submitbutton").removeAttr("disabled");

      if (selectedValue === QUERY_ID_VALUE) {
          $("#id_time").attr("readonly", "readonly");

          if ($endcourse === NO_END_DATE) {
              exibirModalAviso(); // Chama a função assíncrona
              $("#id_submitbutton").attr("disabled", "disabled");
              $("#id_time").val("");
          } else {
              $("#id_time").val(converteTimestamp($endcourse));
          }
      } else {
          $("#id_time").removeAttr("readonly");
      }
  };

  // Retorna o objeto com a função init
  return {
      init: function($endcourse) {
          $("#id_notification_query_id").change(function() {
              verificaNottificacaoTipo($endcourse);
          });

          $(document).ready(function() {
              verificaNottificacaoTipo($endcourse);
          });
      }
  };
});