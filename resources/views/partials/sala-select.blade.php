<button type="button" class="btn btn-success btn-sm mr-2 sala-adicionar-btn" data-id="{{ $submission->id }}">
  <i class="fas fa-thumbs-up"></i>
</button>

@once
  @section('javascripts_bottom')
    @parent
    <div class="modal fade" id="adicionar-sala-modal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalLabel">Indicar sala para reserva</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form">
              <div class="mb-3">
                <select name="accepted" class="form-control form-control-sm">
                  <option value="0">Selecione uma sala...</option>
                  <option value="accepted-g1">Sala G1</option>
                  <option value="accepted-g2">Sala G2</option>
                  <option value="accepted-g3">Sala G3</option>
                </select>
              </div>

              <div>
                <div class="float-right">
                  <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancelar</button>
                  <button type="button" class="btn btn-sm btn-primary submit-btn">Salvar</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script>
      $(document).ready(function() {
        var selectSalaModal = $('#adicionar-sala-modal');
        var formId;

        $('.sala-adicionar-btn').on('click', function() {
          var submissionId = $(this).data('id');
          selectSalaModal.modal();
          formId = '#form-' + submissionId;
        });

        selectSalaModal.find('.submit-btn').on('click', function() {
          var sala = selectSalaModal.find(':input[name=accepted]').val();
          if (sala === "0") {
            alert('Por favor, selecione uma sala!');
            return;
          }

          var sala_select = $('<input type="hidden" name="accepted" value="' + sala + '">');
          $(formId).append(sala_select);

          $(formId).submit();
        });
      });
    </script>
  @endsection
@endonce