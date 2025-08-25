import React from "react";
import AuthLayout from "../layouts/auth-layout";
import { LoginForm } from "@/components/ui/auth/login-form";
import { ResetPasswordForm } from "@/components/ui/auth/reset-password-form";

const Login = ({code}) => {
    return (
        <AuthLayout>
            <div className="flex  w-full h-full items-center">
                <ResetPasswordForm code={code} />
            </div>
        </AuthLayout>
    );
};

export default Login;
