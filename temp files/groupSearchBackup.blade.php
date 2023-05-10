$query = '';  // to pass null string if no query has been passed
        if ($request->has('query')) {
            $query = $request->input('query');
            if (empty($query)) {
                return redirect('groups')->with('status', 'No Valid Paramer To Search');
            }
            // Perform the search query
            $groups = GroupList::whereIn('id', $groupsIdArray)
                ->where(function ($query) {
                    $query->where('title', 'like', '%' . $query . '%')
                        ->orWhere('discription', 'like', '%' . $query . '%')
                        ->orWhere('total_members', 'like', '%' . $query . '%');
                })->paginate(10);
        }