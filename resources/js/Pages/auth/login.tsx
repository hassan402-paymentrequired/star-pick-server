import React from "react";
import AuthLayout from "../layouts/auth-layout";
import { LoginForm } from "@/components/ui/auth/login-form";

const Login = () => {
    return (
        <AuthLayout>
            <div className="flex w-full h-full items-center">
                <LoginForm />
            </div>
        </AuthLayout>
    );
};

export default Login;
