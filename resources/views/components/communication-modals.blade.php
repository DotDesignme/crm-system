@props(['entity', 'type'])

<div class="modal-overlay" id="logCommModal">
    <div class="modal" style="max-width: 450px;">
        <div class="modal-header">
            <h3 id="commModalTitle">{{ __('messages.log_call') }}</h3>
            <button class="modal-close" onclick="document.getElementById('logCommModal').classList.remove('show')">&times;</button>
        </div>
        <form method="POST" action="{{ route('communications.store') }}">
            @csrf
            <input type="hidden" name="communication_type" id="commType" value="call">
            <input type="hidden" name="communicable_type" value="{{ get_class($entity) }}">
            <input type="hidden" name="communicable_id" value="{{ $entity->id }}">
            
            <div class="form-group">
                <label>{{ __('messages.subject') }}</label>
                <input type="text" name="subject" class="form-control" id="commSubject" placeholder="e.g. Initial Call">
            </div>
            
            <div class="form-group">
                <label>{{ __('messages.content') }} / {{ __('messages.notes') }} *</label>
                <textarea name="content" class="form-control" rows="4" required placeholder="{{ __('messages.add_note_placeholder') }}"></textarea>
            </div>

            <div id="callFields" style="display: none;">
                <div class="form-group">
                    <label>{{ __('messages.duration_mins') }}</label>
                    <input type="number" name="metadata[duration]" class="form-control" placeholder="0">
                </div>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('logCommModal').classList.remove('show')">{{ __('messages.cancel') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openLogModal(type) {
    const modal = document.getElementById('logCommModal');
    const title = document.getElementById('commModalTitle');
    const typeInput = document.getElementById('commType');
    const callFields = document.getElementById('callFields');
    const subject = document.getElementById('commSubject');

    typeInput.value = type;
    if(callFields) callFields.style.display = (type === 'call') ? 'block' : 'none';
    
    if(type === 'call') {
        title.innerText = "{{ __('messages.log_call') }}";
        subject.placeholder = "e.g. Follow-up Call";
    } else if(type === 'whatsapp') {
        title.innerText = "{{ __('messages.log_wa') }}";
        subject.placeholder = "e.g. WhatsApp Update";
    } else {
        title.innerText = "{{ __('messages.log_email') }}";
        subject.placeholder = "e.g. Project Proposal Email";
    }

    modal.classList.add('show');
}

function logWhatsApp(phone, communicableType, communicableId) {
    fetch("{{ route('communications.whatsapp.log') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            phone: phone,
            communicable_type: communicableType,
            communicable_id: communicableId
        })
    }).then(response => response.json())
      .then(data => {
          if (data.success) {
              console.log('WhatsApp logged');
              // Optional: reload timeline if needed, but usually a simple redirect or just silent log is fine
          }
      }).catch(err => console.error(err));
}
</script>
@endpush
