// resources/js/Components/WithdrawModal.jsx
import { useState } from "react";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { useForm } from "@inertiajs/react";

export default function WithdrawModal({ open, onClose }) {
    const { data, setData, post, processing, reset } = useForm({
        amount: "",
        account_number: "",
        bank_name: "",
    });

    const handleWithdraw = (e) => {
        e.preventDefault();
        post(route("withdraw.store"), {
            onSuccess: () => {
                reset();
                onClose();
            },
        });
    };

    return (
        <Dialog open={open} onOpenChange={onClose}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Withdraw Funds</DialogTitle>
                </DialogHeader>

                <form onSubmit={handleWithdraw} className="space-y-4">
                    <div>
                        <Label>Amount</Label>
                        <Input
                            type="number"
                            value={data.amount}
                            onChange={(e) => setData("amount", e.target.value)}
                            required
                        />
                    </div>
                    <div>
                        <Label>Bank Name</Label>
                        <Input
                            type="text"
                            value={data.bank_name}
                            onChange={(e) => setData("bank_name", e.target.value)}
                            required
                        />
                    </div>
                    <div>
                        <Label>Account Number</Label>
                        <Input
                            type="text"
                            value={data.account_number}
                            onChange={(e) => setData("account_number", e.target.value)}
                            required
                        />
                    </div>

                    <DialogFooter>
                        <Button type="submit" disabled={processing}>
                            {processing ? "Processing..." : "Withdraw"}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
