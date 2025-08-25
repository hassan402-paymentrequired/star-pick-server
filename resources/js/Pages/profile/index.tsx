import React, { useEffect } from "react";
import MainLayout from "../layouts/main-layout";
import { Avatar, AvatarImage, AvatarFallback } from "@/components/ui/avatar";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { usePage, useForm } from "@inertiajs/react";
// import { Transition } from '@headlessui/react';
import { FormEventHandler } from "react";
import FormError from "@/components/error";
import DeleteUser from "@/components/delete-user";

type ProfileForm = {
    username: string;
    phone: string;
};

const Profile = () => {
    const {
        auth: { user },
    } = usePage<{auth: {user: ProfileForm}}>().props;

    const { data, setData, patch, errors, processing } =
        useForm<Required<ProfileForm>>({
            username: user.username,
            phone: user.phone,
        });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        patch(route("profile.update"), {
            preserveScroll: true,
        });
    };

    return (
        <MainLayout>
            <div className="space-y-2 p-5">
                <header>
                    <h3 className="mb-0.5 text-xl font-medium">
                        Profile information
                    </h3>
                </header>

                <form onSubmit={submit} >
                    <div className="grid gap-1">
                        <Label htmlFor="name">Name</Label>

                        <Input
                            id="name"
                            className="mt-1 block w-full"
                            value={data.username}
                            onChange={(e) => setData("username", e.target.value)}
                            required
                            autoComplete="name"
                            placeholder="Full name"
                        />

                        <FormError classNames="mt-2" message={errors.name} />
                    </div>

                    <div className="grid gap-1">
                        <Label htmlFor="phone">Phone</Label>

                        <Input
                            id="phone"
                            type="text"
                            className="mt-1 block w-full"
                            value={data.phone}
                            onChange={(e) => setData("phone", e.target.value)}
                            required
                            autoComplete="phone"
                            placeholder="Phone no."
                        />

                        <FormError classNames="mt-2" message={errors.phone} />
                    </div>

                    <div className="flex items-center gap-4">
                        <Button disabled={processing}>Save</Button>
                    </div>
                </form>
            <DeleteUser />
            </div>

        </MainLayout>
    );
};

export default Profile;
