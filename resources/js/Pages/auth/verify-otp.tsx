import React from "react";
import AuthLayout from "../layouts/auth-layout";
import { OtpVerification } from "@/components/ui/auth/verify-otp-form";

const Login = () => {
    return (
        <AuthLayout>
            <div className="flex w-full h-full items-center justify-center">
                <OtpVerification />
            </div>
        </AuthLayout>
    );
};

export default Login;
