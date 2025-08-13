<div class="card shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table id="datatable" class="table table-striped mb-0">
                <thead>
                <tr>
                    <th class="align-middle w-120">State ID</th>
                    <th class="align-middle w-120">Updated By</th>
                    <th class="align-middle w-120">Zoned School</th>
                </tr>
                </thead>
                <tbody>
                @forelse($data['address_overwrite'] as $key=>$val)
                    <tr>
                        <td class="align-middle">{{$val->state_id}}</td>
                        <td class="align-middle">{{getUserName($val->user_id)}}</td>
                        <td class="align-middle">{{$val->zoned_school}}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" align="center">No data found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>