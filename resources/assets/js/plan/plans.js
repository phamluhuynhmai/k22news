document.addEventListener('turbo:load', loadPlanData)

function loadPlanData () {
    $('#planCurrency').select2({
        language: {
            noResults: function (params) {
                return Lang.get('messages.no_results_found');
            }
        },
        width: '100%',
    }) ;   
    $('#planFrequency').select2({
        language: {
            noResults: function (params) {
                return Lang.get('messages.no_results_found');
            }
        },
        width: '100%',
    })
}

listen('click', '.plan-delete-btn', function (event) {
    let deletePlanId = $(event.currentTarget).data('id')
        deleteItem(route('plans.destroy', deletePlanId), Lang.get('messages.plans.plan'))
})

listen('change', '.is_default', function (event) {
    let planId = $(event.currentTarget).data('id')
    $.ajax({
        type: 'PUT',
        url: route('plan.make-default', planId),
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message)
                Livewire.emit('refresh')
            }
        },
    })
})
