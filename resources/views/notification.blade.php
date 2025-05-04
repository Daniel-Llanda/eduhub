<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight">
                {{ __('Notifications') }}
            </h2>
            
        </div>
    </x-slot>
    @if ($permissions->isEmpty())
        <div class="text-center text-gray-500 py-4">
            No recent activity
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3">
            @foreach ($permissions as $perm) 
                <div class="bg-white dark:bg-dark-eval-1 rounded-2xl flex gap-1 flex-col p-6 relative overflow-hidden mb-2">
                    
                    <div>
                        <p class="text-gray-800 text-base leading-relaxed">
                            <span class="font-bold text-lg dark:text-gray-200 text-gray-900">{{ $perm->requester->name }}</span><br>
                            <span class="text-sm text-gray-400">requested access to:</span><br>
                            <span class="font-semibold text-blue-600">{{ $perm->post->title }}</span>
                        </p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-400">Status:</span>
                        @if($perm->permission_status == 0)
                            <span class="text-orange-500 font-semibold">Pending</span>
                        @elseif($perm->permission_status == 1)
                            <span class="text-green-500 font-semibold">Approved</span>
                        @else
                            <span class="text-red-500 font-semibold">Denied</span>
                        @endif
                    </div>

                    @if($perm->permission_status == 0)
                        <div class="flex justify-around mt-2">
                            <a href="{{ route('permission_approve', $perm->id) }}" class="bg-green-500 hover:bg-green-600 text-white text-sm px-3 py-1 rounded-lg transition">Approve</a>
                            <a href="{{ route('permission_deny', $perm->id) }}" class="bg-red-500 hover:bg-red-600 text-white text-sm px-3 py-1 rounded-lg transition">Deny</a>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        

        <div class="mb-4">
            <ul class="flex flex-wrap gap-2 text-sm font-medium text-center" id="notif-tab" data-tabs-toggle="#notif-tab-content" role="tablist">
                <li role="presentation">
                    <button
                        class="inline-block px-4 py-2 rounded-full bg-blue-100 text-blue-700 hover:bg-blue-200 dark:bg-blue-900 dark:text-white dark:hover:bg-blue-800 transition"
                        id="requester-tab"
                        data-tabs-target="#requester"
                        type="button"
                        role="tab"
                        aria-controls="requester"
                        aria-selected="false"
                    >
                        Requester 
                    </button>
                </li>
                <li role="presentation">
                    <button
                        class="inline-block px-4 py-2 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 transition"
                        id="comment-tab"
                        data-tabs-target="#comment"
                        type="button"
                        role="tab"
                        aria-controls="comment"
                        aria-selected="false"
                    >
                        Comment
                    </button>
                </li>
            </ul>
        </div>
        
        <div id="notif-tab-content">
            <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="requester" role="tabpanel" aria-labelledby="comment-tab">
                <table id="search-table-requester">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Post</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($permission_approved as $approved)
                            <tr>
                                <td class="font-medium text-gray-900 whitespace-nowrap dark:text-white">{{$approved->requester->name}}</td>
                                <td>{{ $approved->post->title }}</td>
                                <td> 
                                    @if($approved->permission_status == 1)
                                        <span class="text-green-500 font-semibold">Approved</span>
                                    @else
                                        <span class="text-red-500 font-semibold">Denied</span>
                                    @endif
                                </td>
                                <td>{{ $approved->updated_at->format('M d Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="comment" role="tabpanel" aria-labelledby="comment-tab">
                <table id="search-table-comment">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Post</th>
                            <th>Comment</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($comments as $comment)
                            <tr>
                                <td class="font-medium text-gray-900 whitespace-nowrap dark:text-white">{{$comment->user->name}}</td>
                                <td>{{ $comment->post->title }}</td>
                                <td> {{$comment->comment}}</td>
                                <td>{{ $comment->updated_at->format('M d Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        

       


    </div>
    <script>
        $(document).ready(function () {
            // Initialize DataTable if available
            if ($("#search-table-requester").length && typeof simpleDatatables !== 'undefined' && typeof simpleDatatables.DataTable !== 'undefined') {
                const dataTable = new simpleDatatables.DataTable("#search-table-requester", {
                    searchable: true,
                    sortable: true
                });
    
               
            }

            if ($("#search-table-comment").length && typeof simpleDatatables !== 'undefined' && typeof simpleDatatables.DataTable !== 'undefined') {
                const dataTable = new simpleDatatables.DataTable("#search-table-comment", {
                    searchable: true,
                    sortable: true
                });
    
               
            }
            $(".datatable-input, .datatable-selector").addClass("rounded-lg bg-white dark:bg-dark-eval-1");
    
            // Tab switching functionality
            $('#notif-tab button').on('click', function () {
                // Remove active styles from all buttons
                $('#notif-tab button').removeClass('bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-white')
                                        .addClass('bg-gray-100 text-gray-700 dark:bg-gray-700');
    
                // Add active styles to the clicked button
                $(this).addClass('bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-white')
                       .removeClass('bg-gray-100 text-gray-700 dark:bg-gray-700');
    
                // Hide all tab contents
                $('#notif-tab-content > div').addClass('hidden');
    
                // Show the target content
                const target = $(this).data('tabs-target');
                $(target).removeClass('hidden');
            });
    
            // Activate the first tab on page load
            $('#notif-tab button:first').trigger('click');
        });
    </script>
    
    
</x-app-layout>