<div id="data-create-modal" class="modal">
  {!! Form::open(['class'=>'ajax-submit', 'id'=> 'assignTaskForm']) !!}
    <div class="modal-content">
      <a class="btn-floating mb-1 waves-effect waves-light right modal-close"><i class="material-icons">clear</i></a>
      <div class="modal-header"><h4 class="modal-title">Assign Task Form</h4> </div>
      {{ csrf_field() }}
      {!! Form::hidden('documentId', $document->id, ['id' => 'documentId'] ); !!}
      {!! Form::hidden('assignedId', $document->assign->id ?? '', ['id' => 'assignedId'] ); !!}
      {!! Form::hidden('assignRoute', url('documents/task/assign'), ['id' => 'assignRoute'] ); !!}
      <div class="card-body" id="">
        <div class="row">
          <div class="input-field col s12">
            {!! Form::select('assigned_to', $variants->users, '', ['id' => 'assigned_to', 'class' => 'select2 browser-default', 'placeholder'=>'Please select User']) !!}
            <!-- <label for="assigned_to" class="label-placeholder active"> Please select User <span class="red-text">*</span> </label> -->
          </div>
          <div class="input-field col s12">
            {!! Form::textarea('description', '', ['id' => 'description', 'class' => 'materialize-textarea', 'placeholder' => 'Task Details']) !!}
            <!-- <label for="description" class="label-placeholder active"> Task Details </label> -->
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn waves-effect waves-light modal-action modal-close" type="reset" id="resetForm">Close</button>
      <button class="btn cyan waves-effect waves-light" type="submit" name="action" id="formSubmitBtn">Submit <i class="material-icons right">send</i></button>
    </div>
  {{ Form::close() }}
</div>