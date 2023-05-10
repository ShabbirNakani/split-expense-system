rules: {
    title: {
        required: true,
        lettersonly: true
    },
    amount: {
        required: true,
        number: true
    },
    expenseDate: "required",
    // 'expenseUsers[]': {
    //     required: true,
    // },
},
messages: {
    title: "Title field is required.",
    // amount: {
    //     required: "Amount field is required.",
    //     number: "Only numbers are allowed.",
    // },
    expenseDate: "Expense Date is required."
    // 'expenseUsers[]': "Select atleast one member to continue."
},