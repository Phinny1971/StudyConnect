<!-- Reusable Confirmation Modal -->

<div class="modal fade"
     id="confirmationModal"
     tabindex="-1"
     aria-labelledby="confirmationModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div id="confirmationModalHeader"
                 class="modal-header bg-primary text-white">

                <h5 class="modal-title">

                    <i id="confirmationModalIcon"
                       class="bi bi-question-circle-fill me-2"></i>

                    <span id="confirmationModalLabel">
                        Confirmation
                    </span>

                </h5>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                </button>

            </div>

            <div class="modal-body">

                <p id="confirmationModalMessage"
                   class="mb-3">
                </p>

                <ul id="confirmationModalDetails"
                    class="mb-0">
                </ul>

            </div>

            <div class="modal-footer">

                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                    Cancel

                </button>

                <button type="button"
                        id="confirmationModalConfirmBtn"
                        class="btn btn-primary">

                    Confirm

                </button>

            </div>

        </div>

    </div>

</div>

<script>

let confirmationFormId = '';

function showConfirmationModal(options)
{
    confirmationFormId = options.formId;

    document.getElementById('confirmationModalLabel').textContent =
        options.title ?? 'Confirmation';

    document.getElementById('confirmationModalMessage').textContent =
        options.message ?? '';

    // Header colour
    const header =
        document.getElementById('confirmationModalHeader');

    header.className =
        'modal-header ' +
        (options.headerClass ?? 'bg-primary text-white');

    // Close button colour
    const closeButton =
        header.querySelector('.btn-close');

    if ((options.headerClass ?? '').includes('text-dark'))
    {
        closeButton.classList.remove('btn-close-white');
    }
    else
    {
        closeButton.classList.add('btn-close-white');
    }

    // Icon
    const icon =
        document.getElementById('confirmationModalIcon');

    icon.className =
        (options.icon ?? 'bi bi-question-circle-fill') +
        ' me-2';

    // Details
    const details =
        document.getElementById('confirmationModalDetails');

    details.innerHTML = '';

    if (Array.isArray(options.details) &&
        options.details.length > 0)
    {
        options.details.forEach(function(item)
        {
            const li = document.createElement('li');
            li.textContent = item;
            details.appendChild(li);
        });
    }

    // Confirm button
    const confirmButton =
        document.getElementById('confirmationModalConfirmBtn');

    confirmButton.textContent =
        options.confirmText ?? 'Confirm';

    confirmButton.className =
        'btn ' +
        (options.confirmClass ?? 'btn-primary');

    bootstrap.Modal
        .getOrCreateInstance(
            document.getElementById('confirmationModal')
        )
        .show();
}

document
    .getElementById('confirmationModalConfirmBtn')
    .addEventListener('click', function ()
    {
        if (confirmationFormId)
        {
            document
                .getElementById(confirmationFormId)
                .submit();
        }
    });

</script>