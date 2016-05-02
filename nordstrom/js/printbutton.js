function printbutton()
{
    $('#print_rept').show();
    $("#print_rept").print({
        globalStyles: true,
        mediaPrint: false,
        stylesheet: null,
        noPrintSelector: ".no-print",
        iframe: true,
        append: null,
        prepend: null,
        manuallyCopyFormValues: true,
        deferred: $.Deferred()
    });
    $('#print_rept').hide();
}
