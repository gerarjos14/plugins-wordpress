<table class="table table-sm">
    <thead>
        <tr>
            <th scope="col">Tipo</th>
            <th scope="col">Desde</th>
            <th scope="col">Hasta</th>
            <th scope="col">Disponibles</th>
            <th scope="col">F.Autorizacion</th>
            <th scope="col">Estado</th>
        </tr>
    </thead>
    <tbody>

        @isset($cafInvoice)
            <tr>
                <td>{{ \App\Models\Caf::TYPE[$cafInvoice->type] }}</td>
                <td>{{ $folioInvoice->getDesde() }}</td>
                <td>{{ $folioInvoice->getHasta() }}</td>
                <td>{{ $cafInvoice->available }}</td>
                <td>{{ $folioInvoice->getFechaAutorizacion() }}</td>
                <td class="m-2 badge badge-{{$folioInvoice->vigente()?'success':'danger'}}">{{ $folioInvoice->vigente() == 1 ? 'Vigente' : 'Vencido' }}</td>
            </tr>
        @endisset
        @isset($cafBallot)
            <tr>
                <td>{{ \App\Models\Caf::TYPE[$cafBallot->type] }}</td>
                <td>{{ $folioBallot->getDesde() }}</td>
                <td>{{ $folioBallot->getHasta() }}</td>
                <td>{{ $cafBallot->available }}</td>
                <td>{{ $folioBallot->getFechaAutorizacion() }}</td>
                <td class="m-2 badge badge-{{$folioBallot->vigente()?'success':'danger'}}">{{ $folioBallot->vigente() == 1 ? 'Vigente' : 'Vencido' }}</td>
            </tr>
        @endisset
        @isset($cafExent)
        <tr>
                <td>{{ \App\Models\Caf::TYPE[$cafExent->type] }}</td>
                <td>{{ $folioExent->getDesde() }}</td>
                <td>{{ $folioExent->getHasta() }}</td>
                <td>{{ $cafExent->available }}</td>
                <td>{{ $folioExent->getFechaAutorizacion() }}</td>
                <td class="m-2 badge badge-{{$folioExent->vigente()?'success':'danger'}}">{{ $folioExent->vigente() == 1 ? 'Vigente' : 'Vencido' }}</td>
            </tr>
        @endisset
        @isset($cafNotaDebito)
        <tr>
                <td>{{ \App\Models\Caf::TYPE[$cafNotaDebito->type] }}</td>
                <td>{{ $folioNotaDebito->getDesde() }}</td>
                <td>{{ $folioNotaDebito->getHasta() }}</td>
                <td>{{ $cafNotaDebito->available }}</td>
                <td>{{ $folioNotaDebito->getFechaAutorizacion() }}</td>
                <td class="m-2 badge badge-{{$folioNotaDebito->vigente()?'success':'danger'}}">{{ $folioNotaDebito->vigente() == 1 ? 'Vigente' : 'Vencido' }}</td>
            </tr>
        @endisset
        @isset($cafNotaCredito)
        <tr>
            <td>{{ \App\Models\Caf::TYPE[$cafNotaCredito->type] }}</td>
            <td>{{ $folioNotaCredito->getDesde() }}</td>
            <td>{{ $folioNotaCredito->getHasta() }}</td>
            <td>{{ $cafNotaCredito->available }}</td>
            <td>{{ $folioNotaCredito->getFechaAutorizacion() }}</td>
            <td class="m-2 badge badge-{{$folioNotaCredito->vigente()?'success':'danger'}}">{{ $folioNotaCredito->vigente() == 1 ? 'Vigente' : 'Vencido' }}</td>
        </tr>
    @endisset



    </tbody>
</table>
