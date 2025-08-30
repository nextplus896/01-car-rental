<script>
    $(document).on("click",".logout-btn",function(event) {
        event.preventDefault();
        var actionRoute =  "{{ setRoute('admin.logout') }}";
        var target      = "auth()->user()->id";
        var message     = `{{ __("Are you sure to") }} <strong>{{ __("Logout") }}</strong>?`;
        openDeleteModal(actionRoute,target,message,"{{ __('Logout') }}","POST");
    });
</script>
