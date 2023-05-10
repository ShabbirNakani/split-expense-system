$groupId = $request->groupId;
        $expensesIdArray = SplitExpense::whereGroupListId($groupId)->select('expense_id');
        $expenses = Expense::whereIn('id', $expensesIdArray)->orderBy('created_at', 'asc')->get()->toArray();

        // search expseses (live search)
        if ($request->has('search')) {
            // live search logic
            if (!empty($request->search)) {
                $expenses = Expense::whereIn('id', $expensesIdArray)
                    // ->orderBy('created_at', 'asc')
                    ->whereGroupListId($groupId)
                    ->where('title', 'LIKE', '%' . $request->search . "%")
                    ->orWhere('amount', 'LIKE', '%' . $request->search . "%")
                    ->orWhere('expense_date', 'LIKE', '%' . $request->search . "%")
                    ->orWhere('members', 'LIKE', '%' . $request->search . "%")
                    ->get();
            }
        }

        // user filter and date range filter
        if ($request->has('dateRange') || $request->has('filterUser')) {
            // dd($groupId);
            $expenseCreatorId = $request->filterUser;
            $query = Expense::query();
            $query->whereIn('id', $expensesIdArray);
            if ($request->dateRange) {
                // dd('dateRange', $request->dateRange);
                $range =  explode(' - ', $request->dateRange);
                $startDate = \Carbon\Carbon::parse($range[0])->format('Y-m-d H:i:s');
                $endDate = \Carbon\Carbon::parse($range[1])->format('Y-m-d H:i:s');
                // dd($startDate, $endDate);
                $query->whereBetween('expense_date', [$startDate, $endDate]);
            }
            if ($request->filterUser !== 'null') {
                // dd('filterUser', $request->filterUser);
                $query->whereUserId($request->filterUser)->whereGroupListId($request->groupId);
            }
            $expenses = $query->get();
        }
        return response()->json([
            'expenses' => $expenses
        ]);
