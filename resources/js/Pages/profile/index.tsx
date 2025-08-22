import React, { useEffect } from "react";
import MainLayout from "../layouts/main-layout";
import { Avatar, AvatarImage, AvatarFallback } from "@/components/ui/avatar";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { usePage } from "@inertiajs/react";

const Profile = () => {
    const {
        auth: { user },
    } = usePage().props;

 
        if (!user) {
            return (
                <MainLayout>
                    <div className="max-w-md mx-auto p-4 space-y-6">
                        <h2 className="text-lg font-semibold">Loading</h2>
                    </div>
                </MainLayout>
            );
        }


    return (
        <MainLayout>
            <div className="max-w-md mx-auto p-4 space-y-6">
                {/* Profile Header */}
                <div className="flex flex-col items-center gap-3">
                    <Avatar className="w-24 h-24">
                        <AvatarImage
                            src="/images/user-avatar.jpg"
                            alt="User Avatar"
                        />
                        <AvatarFallback>
                            {user?.name?.substring(0, 2)}
                        </AvatarFallback>
                    </Avatar>
                    <h2 className="text-lg font-semibold">{user?.username}</h2>
                    <p className="text-sm text-muted-foreground">
                        {user?.email}
                    </p>
                    <Button variant="secondary" size="sm">
                        Change Photo
                    </Button>
                </div>

                {/* Profile Details */}
                <Card>
                    <CardContent className="p-4 space-y-4">
                        <div className="space-y-2">
                            <Label htmlFor="name">Full Name</Label>
                            <Input id="name" defaultValue={user?.name} />
                        </div>
                        <div className="space-y-2">
                            <Label htmlFor="email">Email</Label>
                            <Input
                                id="email"
                                type="email"
                                defaultValue={user.email}
                            />
                        </div>
                        <div className="space-y-2">
                            <Label htmlFor="phone">Phone Number</Label>
                            <Input id="phone" defaultValue={user.phone} />
                        </div>
                        <Button className="w-full">Save Changes</Button>
                    </CardContent>
                </Card>
            </div>
        </MainLayout>
    );
};

export default Profile;
