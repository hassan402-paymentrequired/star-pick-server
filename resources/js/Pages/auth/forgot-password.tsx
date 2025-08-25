import React from "react";
import AuthLayout from "../layouts/auth-layout";
import { ForgotPassword as Form } from "@/components/ui/auth/forgot-password";

const ForgotPassword = () => {
    return (
        <AuthLayout>
            <div className="flex w-full h-full items-center justify-center">
                <Form />
            </div>
        </AuthLayout>
    );
};

export default ForgotPassword;
