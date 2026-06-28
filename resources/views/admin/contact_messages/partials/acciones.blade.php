<div class="d-flex justify-content-center align-items-center">
    @can('admin.contact-messages.index')
        <button type="button" class="btn btn-outline-primary btn-xs viewContactMessage mr-1" title="Ver detalle"
            data-id="{{ $message->id }}">
            <i class="fas fa-eye"></i>
        </button>
    @endcan

    @can('admin.contact-messages.update')
        @if ($message->status !== 'read')
            <button type="button" class="btn btn-outline-secondary btn-xs markContactRead mr-1" title="Marcar como leido"
                data-id="{{ $message->id }}">
                <i class="fas fa-envelope-open"></i>
            </button>
        @endif

        @if ($message->status !== 'answered')
            <button type="button" class="btn btn-outline-success btn-xs markContactAnswered mr-1" title="Marcar como respondido"
                data-id="{{ $message->id }}">
                <i class="fas fa-check-double"></i>
            </button>
        @endif

        @if ($message->status !== 'new')
            <button type="button" class="btn btn-outline-info btn-xs markContactNew mr-1" title="Marcar como nuevo"
                data-id="{{ $message->id }}">
                <i class="fas fa-bell"></i>
            </button>
        @endif
    @endcan

    @can('admin.contact-messages.destroy')
        <button type="button" class="btn btn-outline-danger btn-xs deleteContactMessage" title="Eliminar mensaje"
            data-id="{{ $message->id }}" data-name="{{ $message->full_name }}">
            <i class="fas fa-trash"></i>
        </button>
    @endcan
</div>
