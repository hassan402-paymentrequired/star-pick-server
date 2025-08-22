import React from "react";
import MainLayout from "../layouts/main-layout";
import { Head, usePage } from "@inertiajs/react";
import { Button } from "@/components/ui/button";
import { Deposit } from "./deposit";
import { ArrowUpCircle, ArrowDownCircle } from "lucide-react";
import WithdrawModal from "./withdraw";

const Wallet = ({ transactions, banks }) => {
    const {
        auth: { user },
    } = usePage().props;

    const formatAmount = (amount, type) => {
        const color = type === "credit" ? "text-green-500" : "text-red-500";
        return <span className={`font-semibold ${color}`}>₦{parseFloat(amount).toLocaleString()}</span>;
    };

    const formatDate = (date) => {
        return new Date(date).toLocaleString();
    };

    return (
        <MainLayout>
            <Head title="Wallet" />
            <div className="flex flex-col items-center px-4 py-6">
                {/* Balance Card */}
                <div className="w-full max-w-md rounded-xl p-5  mb-6">
                    <h2 className="text-lg text-gray-400">Current Balance</h2>
                    <p className="text-4xl font-extrabold mt-2 tracking-wide">
                        ₦{parseFloat(user.wallet.balance).toLocaleString()}
                    </p>
                </div>

                {/* Action Buttons */}
                <div className="w-full max-w-md grid grid-cols-2 gap-4 mb-8">
                    <Deposit />
                    <WithdrawModal banks={banks}/>
                </div>

                {/* Recent Transactions */}
                <div className="w-full max-w-md">
                    <h3 className="text-lg font-semibold text-white mb-3">Recent Transactions</h3>
                    {transactions.length === 0 ? (
                        <div className="text-center text-gray-400 text-sm py-6 border border-dashed border-gray-500 rounded-lg">
                            No transactions yet.
                        </div>
                    ) : (
                        <div className="space-y-3">
                            {transactions.map((t) => (
                                <div
                                    key={t.id}
                                    className="flex items-center justify-between p-3 bg-gray-800 rounded-lg shadow-sm"
                                >
                                    <div className="flex items-center gap-3">
                                        {t.action_type === "credit" ? (
                                            <ArrowDownCircle className="text-green-500" size={20} />
                                        ) : (
                                            <ArrowUpCircle className="text-red-500" size={20} />
                                        )}
                                        <div>
                                            <p className="text-sm font-medium text-white">{t.description}</p>
                                            <p className="text-xs text-gray-400">{formatDate(t.created_at)}</p>
                                        </div>
                                    </div>
                                    <div>{formatAmount(t.amount, t.action_type)}</div>
                                </div>
                            ))}
                        </div>
                    )}
                </div>
            </div>
        </MainLayout>
    );
};

export default Wallet;
