import React from 'react'
import MainLayout from '../layouts/main-layout'
import { Head } from '@inertiajs/react'

const Wallet = () => {
  return (
    <MainLayout>
        <Head title="Wallet" />
        <div className="flex flex-col items-center justify-center  px-1">
            <div className="w-full max-w-md p-3 flex flex-col items-center">
                <div className="flex items-center gap-3 mb-4">

                    <h2 className="text-2xl font-bold text-muted-white">My Wallet</h2>
                </div>
                <div className="w-full flex flex-col items-center mb-6">
                    <span className="text-muted text-sm">Current Balance</span>
                    <span className="text-4xl font-extrabold text-primary mt-1 mb-2 tracking-wide">
                        $0.00
                    </span>
                    <span className="text-xs text-muted">No funds yet</span>
                </div>
                <div className="w-full flex gap-4 mb-6">
                    <button className="flex-1 py-2 rounded bg-primary text-white font-semibold hover:bg-primary/90 transition">
                        Deposit
                    </button>
                    <button className="flex-1 py-2 rounded bg-muted text-muted-white font-semibold hover:bg-muted/80 transition">
                        Withdraw
                    </button>
                </div>
                <div className="w-full">
                    <h3 className="text-lg font-semibold text-muted-white mb-2">Recent Transactions</h3>
                    <div className="flex flex-col gap-2">
                        <div className="text-center text-muted text-sm py-6 border border-dashed border-muted rounded-lg">
                            No transactions yet.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
  )
}

export default Wallet
