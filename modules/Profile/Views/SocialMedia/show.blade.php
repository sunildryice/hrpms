 <div class="card">
     <div class="card-header fw-bold">
         Social Media
     </div>

     <div class="card-body">
         <div class="table-responsive">
             <table class="table table-bordered" id="socialMediaTable">
                 <tbody>
                     <div>
                         @foreach ($socialMediaAccounts as $account)
                             <tr>
                                 <th scope="row" width="10%">
                                     {{ $account->title }}</th>
                                 <td colspan="3">
                                     <a target="_blank"
                                         href="{{ $account->link }}">{{ $socialMediaLinks[$account->title] ?? '' }}</a>
                                 </td>
                             </tr>
                         @endforeach
                         <tr>
                             <th scope="row" width="10%">Bio</th>
                             <td colspan="3">{{ $employee->bio }}</td>
                         </tr>

                     </div>
                 </tbody>
             </table>
         </div>
     </div>
 </div>
