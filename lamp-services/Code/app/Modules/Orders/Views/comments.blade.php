<table class="table table-hover table-bordered table-striped thline">
	<thead>
		<tr>
			<th>Date</th>
			<th>Status</th>
			<th>By</th>
			<th>Comment</th>
		</tr>
	</thead>
	@if(is_array($commentArr) && count($commentArr) > 0)
		@foreach($commentArr as $comment)
		<tr>
			<td>@if($comment->comment_date !='' && $comment->comment_date !='0000-00-00 00:00:00')
				{{date('d/m/Y h:i A', strtotime($comment->comment_date))}}
				@endif
			</td>
			<td>{{(isset($commentStatusArr[$comment->order_status_id]) ? $commentStatusArr[$comment->order_status_id] : '')}}</td>
			<td>{{$comment->user_name}}</td>
			<td>{{$comment->comment}}</td>
		</tr>
		@endforeach
	@else
		<tr>
			<td colspan="4">No Comment Found</td>
		</tr>
	@endif		
</table>
