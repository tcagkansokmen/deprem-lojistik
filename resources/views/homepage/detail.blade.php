<table class="table table-stripped">
    <thead>
        <tr>
            <th>Kategori</th>
            <th>Ürün</th>
            <th>Miktar</th>
        </tr>
    </thead>
    <tbody>
        @foreach($vehicles->contents as $content)
            <tr>
                <td>{{ $content->category->name }}</td>
                <td>{{ $content->product->name }}</td>
                <td>{{ $content->quantity }} {{ $content->unit }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
