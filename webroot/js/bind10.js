function zona_addGoogleApps() {
    var zona = document.getElementById('zonaField').value;
    var registros = [
        ['', 'MX', '1 aspmx.l.google.com.'],
        ['', 'MX', '2 alt1.aspmx.l.google.com.'],
        ['', 'MX', '2 alt2.aspmx.l.google.com.'],
        ['', 'MX', '3 aspmx2.googlemail.com.'],
        ['', 'MX', '3 aspmx3.googlemail.com.'],
        ['', 'MX', '3 aspmx4.googlemail.com.'],
        ['', 'MX', '3 aspmx5.googlemail.com.'],
        ['mail.', 'CNAME', 'ghs.google.com.'],
        ['calendar.', 'CNAME', 'ghs.google.com.'],
        ['docs.', 'CNAME', 'ghs.google.com.']
    ];
    for (i=0; i<registros.length; ++i) {
        var registro = '<tr><td style="display:none;"><div><input type="hidden" name="id[]" value="" /></div></td>'+
            '<td><div><input type="text" name="name[]" value="'+registros[i][0]+zona+'" id="name[]Field" class="form-control check notempty"  /></div></td>'+
            '<td><div><input type="text" name="rdtype[]" value="'+registros[i][1]+'" id="rdtype[]Field" class="form-control check notempty"  /></div></td>'+
            '<td><div><input type="text" name="rdata[]" value="'+registros[i][2]+'" id="rdata[]Field" class="form-control check notempty"  /></div></td>'+
            '<td><a href="" onclick="$(this).parent().parent().remove(); return false" title="Eliminar"><span class="fa fa-remove btn btn-default" aria-hidden="true"></span></a></td></tr>';
        $('#records').append(registro);
    }
}
