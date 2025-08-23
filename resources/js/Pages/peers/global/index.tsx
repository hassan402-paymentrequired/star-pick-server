import { Button } from "@/components/ui/button";
import MainLayout from "@/Pages/layouts/main-layout";
import { Head, Link, router, usePage } from "@inertiajs/react";
import React from "react";

const Global = ({ tournament, users }) => {
    console.log(users);
    const {
        auth: {
            user: { id },
        },
    } = usePage().props;

    const isAmoung = () => {
        return users.some((user) => user.id.toString() === id.toString());
    };

    return (
        <MainLayout>
            <Head title="Global contest" />

            <div className="flex flex-col bg-stone-100 h-screen">
                <div className="flex items-center justify-between p-3">
                    <div className="flex flex-col items-start mt-3 mb-2">
                        <h2 className="text-base capitalize  font-bold text-muted-white">
                            {tournament.name}'s
                        </h2>
                        <p className="text-muted text-xs font-semibold">
                            Join other users and compete globally!
                        </p>
                    </div>
                    <div>₦{tournament.amount}</div>
                </div>

                {!isAmoung() ? (
                    <div className="flex justify-center py-8">
                        <div className="p-6 flex flex-col items-center max-w-xs">
                            <span className="text-4xl mb-2 animate-bounce">
                                🌍
                            </span>
                            <div className="text-center text-muted mb-3 font-semibold">
                                You haven't joined {tournament.name} yet!
                            </div>
                            <p className="text-center text-muted mb-4">
                                Be part of the excitement—join the contest and
                                compete with other players.
                            </p>
                            <Link
                                href={route("tournament.create")}
                                className="inline-flex cursor-pointer items-center gap-2 text-sm font-medium text-primary transition"
                                prefetch
                            >
                                <Button className="text-foreground">
                                    <span>Join {tournament.name}</span>
                                    <span className="text-lg">⚔️</span>
                                </Button>
                            </Link>
                        </div>
                    </div>
                ) : (
                    <div className="w-full h-screen bg-white">
                        <table className="w-full ">
                            <thead className="">
                                <tr className="">
                                    <th
                                        className="text-xs px-2 py-2 text-start text-stone-500"
                                    >
                                        Pos
                                    </th>
                                    <th
                                        className="text-xs text-start text-stone-500"
                                        colSpan={4}
                                    >
                                        @User
                                    </th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th
                                        className="text-xs text-end px-2 text-stone-500"
                                    >
                                        Points
                                    </th>
                                </tr>
                            </thead>

                            <tbody className="divider-y">
                                
                                {users.map((user, i) => (
                                    <tr key={i} className=" px-2 cursor-pointer" onClick={() => router.visit(route('tournament.user.show', {user: user.id}))}>
                                        <td
                                            className="text-sm py-2 px-2 text-start font-bold"
                                        >
                                            {i + 1}
                                        </td>
                                        <td
                                            className="text-sm text-start font-bold"
                                            colSpan={4}
                                        >
                                            @{user.username}
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td
                                            className="text-sm text-end pr-4 font-bold"
                                            colSpan={4}
                                        >
                                            {user.total_point}
                                        </td>
                                    </tr>
                                ))}
                                
                            </tbody>
                        </table>
                    </div>
                )}
            </div>
        </MainLayout>
    );
};

export default Global;
