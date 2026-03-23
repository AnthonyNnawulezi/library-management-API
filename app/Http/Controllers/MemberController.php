<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Http\Resources\MemberResource;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Member::with('activeBorrowings');
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        });

        // if ($request->has('search')) { above is better for grouping so that $status below can apply to all of them
        //     // $search = $request->search; 
        //     $query->where('name', 'like', "%$search%")
        //         ->orWhere('email', 'like', "%$search%");
        // }

        if ($request->has('status')) {
            $status = $request->status;
            $query->where('status', $status);
        }
        $members = $query->paginate(10);
        // return response()->json($members);   
        return MemberResource::collection($members);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMemberRequest $request)
    {
        $member = Member::create($request->validated());
        return new MemberResource($member);
    }

    /**
     * Display the specified resource.
     */
    public function show(Member $member)
    {
        $member->load(['borrowings', 'activeBorrowings']);
        return new MemberResource($member);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMemberRequest $request, Member $member)
    {
        $member->update($request->validated());
        return new MemberResource($member);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
        if ($member->activeBorrowings()->count() > 0) {
            return response()->json(['status' => 'error', 'message' => 'Cannot delete member with active borrowings'], 422);
        }
        $member->delete();
        return response()->json(['status' => 'success', 'message' => 'Member deleted successfully']);
    }
}
