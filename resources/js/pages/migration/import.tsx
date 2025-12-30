import { Button } from '@/components/ui/button';
import { home, login, register } from '@/routes';
import { type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import clsx from 'clsx';
import { useEffect, useMemo, useRef, useState } from 'react';

type StepStatus = "idle" | "running" | "success" | "error" | "skipped";
type StepNumber = number;
type Step = Record<number, string>
type ImportProps = SharedData & {
    steps: Step
}

export default function Import({
    canRegister = true,
}: {
    canRegister?: boolean;
}) {
    const { auth } = usePage<SharedData>().props;

    const { steps: stepsObj = {} } = usePage<ImportProps>().props;

    // Sort by numeric step number
    const orderedKeys = useMemo(
        () => Object.keys(stepsObj).map(Number).sort((a, b) => a - b),
        [stepsObj]
    );

    // State
    const [currentStep, setCurrentStep] = useState<StepNumber | null>(null);
    const [isRunning, setIsRunning] = useState(false);
    const [statuses, setStatuses] = useState<Record<StepNumber, StepStatus>>({});
    const [errors, setErrors] = useState<Record<StepNumber, string>>({});
    const cancelRef = useRef(false);

    // Helpers
    const setStatus = (n: StepNumber, s: StepStatus, err?: string) => {
        setStatuses((prev) => ({ ...prev, [n]: s }));
        if (err) setErrors((e) => ({ ...e, [n]: err }));
    };

    const reset = () => {
        const seed: Record<StepNumber, StepStatus> = {};
        for (const n of orderedKeys) seed[n] = "idle";
        setStatuses(seed);
        setErrors({});
        setCurrentStep(null);
        cancelRef.current = false;
    };

    // Run one step
    const deploymentStep = async (n: StepNumber) => {
        const step = stepsObj[n];
        const endpoint = `/import-old?step=${n}`;
        const res = await fetch(endpoint, {
            method: "GET",
            headers: { "Content-Type": "application/json" },
        });
        if (!res.ok) {
        let msg = res.statusText;
        try {
            const j = await res.json();
            msg = j?.message || j?.error || msg;
        } catch {}
        throw new Error(msg || `Step ${n} failed`);
        }
    };

    // Run all steps sequentially
    const startDeployment = async () => {
        if (isRunning || orderedKeys.length === 0) return;
        reset();
        setIsRunning(true);

        try {
        for (const n of orderedKeys) {
            if (cancelRef.current) {
            // mark remaining as skipped
            for (const m of orderedKeys.filter((k) => !statuses[k] || statuses[k] === "idle"))
                setStatus(m, "skipped");
            break;
            }
            setCurrentStep(n);
            setStatus(n, "running");
            try {
            await deploymentStep(n);
            setStatus(n, "success");
            } catch (e: any) {
            setStatus(n, "error", e?.message || "Unknown error");
            break; // remove this line if you want to continue after errors
            }
        }
        } finally {
        setIsRunning(false);
        setCurrentStep(null);
        }
    };

    const cancel = () => {
        if (!isRunning) return;
        cancelRef.current = true;
    };

    const retryFailed = async () => {
        if (isRunning) return;
        const failed = orderedKeys.filter((n) => statuses[n] === "error");
        if (!failed.length) return;
        cancelRef.current = false;
        setIsRunning(true);
        try {
        for (const n of failed) {
            if (cancelRef.current) break;
            setCurrentStep(n);
            setStatus(n, "running");
            try {
            await deploymentStep(n);
            setStatus(n, "success");
            } catch (e: any) {
            setStatus(n, "error", e?.message || "Unknown error");
            break;
            }
        }
        } finally {
        setIsRunning(false);
        setCurrentStep(null);
        }
    };

    // Tiny UI (swap with your design)
    const total = orderedKeys.length || 1;
    const done = orderedKeys.filter((n) => statuses[n] === "success").length;
    const progress = Math.round((done / total) * 100);


    return (
        <>
            <Head title="Import">
                <link rel="preconnect" href="https://fonts.bunny.net" />
                <link
                    href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600"
                    rel="stylesheet"
                />
            </Head>
            <div className="flex min-h-screen flex-col items-center bg-[#FDFDFC] p-6 text-[#1b1b18] lg:justify-center lg:p-8 dark:bg-[#0a0a0a]">
                <header className="mb-6 w-full max-w-[335px] text-sm not-has-[nav]:hidden lg:max-w-4xl">
                    <nav className="flex items-center justify-end gap-4">
                        {auth.user ? (
                            <Link
                                href={home()}
                                className="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                            >
                                Dashboard
                            </Link>
                        ) : (
                            <>
                                <Link
                                    href={login()}
                                    className="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
                                >
                                    Log in
                                </Link>
                                {canRegister && (
                                    <Link
                                        href={register()}
                                        className="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                                    >
                                        Register
                                    </Link>
                                )}
                            </>
                        )}
                    </nav>
                </header>
                <div className="flex w-full items-center justify-center opacity-100 transition-opacity duration-750 lg:grow starting:opacity-0">
                    <main className="flex w-full max-w-xl flex-col-reverse lg:max-w-4xl lg:flex-row">
                        <div className="flex-1 rounded-br-lg rounded-bl-lg bg-white p-6 pb-12 text-[13px] leading-[20px] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] lg:rounded-tl-lg lg:rounded-br-none lg:p-20 dark:bg-[#161615] dark:text-[#EDEDEC] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]">
                            <h1 className="mb-1 font-medium">
                                {isRunning ? "Running..." : "Start Deployment"}
                            </h1>
                            <p className="mb-2 text-[#706f6c] dark:text-[#A1A09A]">
                                Progress: {progress}%
                            </p>
                            <ul className="mb-4 flex flex-col lg:mb-6 h-12">
                                {orderedKeys.map((n) => {

                                    const s = statuses[n] || "idle";
                                    const isActive = currentStep === n && isRunning;
                                    const step = stepsObj[n];
                                    return <li key={n} className={clsx("relative flex items-center gap-4 py-2 before:absolute before:top-1/2 before:bottom-0 before:left-[0.4rem] before:border-l before:border-[#e3e3e0] dark:before:border-[#3E3E3A]",{
                                        "flex": isActive,
                                        "hidden": !isActive,
                                    })}>
                                        <span className="relative bg-white py-1 dark:bg-[#161615]">
                                            <span className="flex h-3.5 w-3.5 items-center justify-center rounded-full border border-[#e3e3e0] bg-[#FDFDFC] shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] dark:border-[#3E3E3A] dark:bg-[#161615]">
                                                <span className={clsx("h-1.5 w-1.5 rounded-full",{
                                                    "bg-white dark:bg-white": s == 'idle',
                                                    "bg-blue-500 dark:bg-blue-900": s == 'running',
                                                    "bg-green-500 dark:bg-green-900": s == 'success',
                                                    "bg-red-500 dark:bg-red-900": s == 'error',
                                                    "bg-[#dbdbd7] dark:bg-[#3E3E3A]": s == 'skipped',
                                                })} />
                                            </span>
                                        </span>
                                        <span>
                                            {step}
                                        </span>
                                    </li>
                                })}
                            </ul>
                            <ul className="flex flex-row gap-3 text-sm leading-normal">
                                <li>
                                    <Button
                                        onClick={startDeployment}
                                        disabled={isRunning || Object.keys(stepsObj).length === 0}>
                                        Deploy now
                                    </Button>
                                </li>
                                <li>
                                    <Button
                                        onClick={cancel}
                                        disabled={!isRunning} >
                                        Cancel
                                    </Button>
                                </li>
                                <li>
                                    <Button
                                        onClick={retryFailed}
                                        disabled={
                                            isRunning || !orderedKeys.some((n) => statuses[n] === "error")
                                        } >
                                        Retry Failed
                                    </Button>
                                </li>
                            </ul>
                        </div>
                    </main>
                </div>
                <div className="hidden h-14.5 lg:block"></div>
            </div>
        </>
    );
}
