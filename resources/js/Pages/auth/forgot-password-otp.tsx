import React from "react";
import AuthLayout from "../layouts/auth-layout";
import { ForgotPasswordOtp } from "@/components/ui/auth/forgot-password-otp";

const ForgotPasswordForm = () => {
    return (
        <AuthLayout>
            <div className="flex w-full h-full items-center justify-center">
                <ForgotPasswordOtp />
            </div>
        </AuthLayout>
    );
};

export default ForgotPasswordForm;
