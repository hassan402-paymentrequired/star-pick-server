import { Button } from "@/components/ui/button";
import MainLayout from "@/Pages/layouts/main-layout";
import { Head, Link } from "@inertiajs/react";
import React from "react";

const Global = () => {
    return (
        <MainLayout>
            <Head title="Global contest" />

            <div className="flex flex-col">
                <div className="flex flex-col items-start mb-6">
                    <h2 className="text-2xl font-bold text-muted-white mb-1">
                        Today's Challenges
                    </h2>
                    <p className="text-muted text-base">
                        Join other users and compete globally!
                    </p>
                </div>

                {true && (
                    <div className="flex justify-center py-8">
                        <div className="p-6 flex flex-col items-center max-w-xs">
                            <span className="text-4xl mb-2 animate-bounce">
                                🌍
                            </span>
                            <div className="text-center text-muted mb-3 font-semibold">
                                You haven't joined any global contests yet!
                            </div>
                            <p className="text-center text-muted mb-4">
                                Be part of the excitement—join a global contest and compete with other players.
                            </p>
                            <Link
                                href="/peers"
                                className="inline-flex cursor-pointer items-center gap-2 text-sm font-medium text-primary transition"
                            >
                                <Button>
                                    <span>Explore Global Contests</span>
                                    <span className="text-lg">🚀</span>
                                </Button>
                            </Link>
                        </div>
                    </div>
                )}
            </div>
        </MainLayout>
    );
};

export default Global;
