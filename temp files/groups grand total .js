// attach the details to the settel modal
data.groupsWithExpenses.forEach(group => {

    let reminingAmount = 0;
    let total_owe = 0;
    let total_pay = 0;

    group.expenses.forEach(expense => {

        expense.split_expense.forEach(splitExpense => {
            // console.log('split : ', splitExpense);

            if (splitExpense.receiver_id !==
                splitExpense.user_id) {
                // checking who spent and to whom
                // owe condition
                if ((splitExpense.receiver_id ==
                    authUserId) && (splitExpense
                        .user_id == friendId)) {
                    total_owe += splitExpense.amount;

                } else if ((splitExpense.receiver_id ==
                    friendId) && (splitExpense
                        .user_id == authUserId)) {
                    // pay condition
                    // dump('pay amount =>', $expens->amount);
                    total_pay += splitExpense.amount;

                }
            }
        });
        console.log('owe amount : ', total_owe);
        console.log('pay amount : ', total_pay);
        if (total_owe > total_pay) {
            remainingAmount = total_owe - total_pay;
            console.log('remaining owe : ', remainingAmount);
        } else if (total_owe < total_pay) {
            remainingAmount = total_pay - total_owe;
            console.log('remaining pay : ', remainingAmount);
        } else if (total_owe = total_pay) {
            remainingAmount = 0
        }
    });
    // console.log('owe amount : ',total_owe);
    // console.log('pay amount : ',total_pay);


    $('.append-checkBox').append(`
    <div class="form-check">
        <div class="row">
            <div class="col-8">
                <input class="form-check-input checkBoxCommon" type="checkbox"
                    value="${group.id}" id="groups" name="groups[]">
                <label class="form-check-label" for="flexCheckDefault">
                    ${group.title}
                </label>
            </div>
            <div class="col-4">
                <h4><span class="badge badge-success">${reminingAmount}</span></h4>
            </div>
        </div>
    </div>
    `);
});