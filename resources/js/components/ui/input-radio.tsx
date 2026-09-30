import * as React from "react"

import { cn } from "@/lib/utils"

function RadioInput({ className, ...props }: React.ComponentProps<"input">) {
  return (
    <input
      type="radio"
      data-slot="input"
      className={cn(
        "peer size-4 shrink-0 rounded-full border border-gray-300 dark:border-neutral-700 text-blue-600 dark:text-blue-400 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50",
        className
      )}
      {...props}
    />
  )
}

export { RadioInput }
