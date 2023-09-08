<table>
    <thead>
    <tr>
        <th>Title</th>
		<th>Description</th>
		<th>Tags</th>
		<th>Category</th>
		<th>Variant Name</th>
		<th>Variant Value</th>
		<th>Price</th>
		<th>SKU</th>
		<th>Weight</th>
		<th>Stock</th>
		<th>ShelfLife</th>
		<th>Temp Require</th>
		<th>Height</th>
		<th>Width</th>
		<th>Length</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $row)
	@php $demension=explode("-",$row->dimensions); @endphp
        <tr>
            <td>{{$row->title}}</td>
			<td>{{$row->body_html}}</td>
			<td>{{$row->tags}}</td>
			<td>{{$row->category}}</td>
			<td>{{$row->varient_name}}</td>
			<td>{{$row->varient_value}}</td>
			<td>{{$row->base_price}}</td>
			<td>{{$row->sku}}</td>
			<td>{{$row->grams}}</td>
			<td>{{$row->stock}}</td>
			<td>{{$row->shelf_life}}</td>
			<td>{{$row->temp_require}}</td>
			<td>{{$demension[0]}}</td>
			<td>{{$demension[1]}}</td>
			<td>{{$demension[2]}}</td>
        </tr>
    @endforeach
    </tbody>
</table>