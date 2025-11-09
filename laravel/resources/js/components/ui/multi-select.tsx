import * as React from "react"
import * as DropdownMenuPrimitive from "@radix-ui/react-dropdown-menu"
import * as CheckboxPrimitive from "@radix-ui/react-checkbox"
import { CheckIcon, ChevronDownIcon, XIcon } from "lucide-react"
import { cn } from "@/lib/utils"

interface MultiSelectOption {
  value: string
  label: string
  disabled?: boolean
}

interface MultiSelectProps {
  options: MultiSelectOption[]
  value?: string[]
  onValueChange?: (value: string[]) => void
  placeholder?: string
  className?: string
  disabled?: boolean
  maxDisplay?: number
}

export function MultiSelect({
  options,
  value = [],
  onValueChange,
  placeholder = "Select options...",
  className,
  disabled = false,
  maxDisplay = 3,
}: MultiSelectProps) {
  const [open, setOpen] = React.useState(false)

  const handleSelect = (optionValue: string) => {
    const newValue = value.includes(optionValue)
      ? value.filter((v) => v !== optionValue)
      : [...value, optionValue]
    
    onValueChange?.(newValue)
  }

  const handleClearAll = () => {
    onValueChange?.([])
  }

  const selectedOptions = options.filter((option) => value.includes(option.value))
  const displayText = React.useMemo(() => {
    if (selectedOptions.length === 0) {
      return placeholder
    }
    if (selectedOptions.length <= maxDisplay) {
      return selectedOptions.map((option) => option.label).join(", ")
    }
    return `${selectedOptions.slice(0, maxDisplay).map((option) => option.label).join(", ")} +${selectedOptions.length - maxDisplay} more`
  }, [selectedOptions, placeholder, maxDisplay])

  return (
    <DropdownMenuPrimitive.Root open={open} onOpenChange={setOpen}>
      <DropdownMenuPrimitive.Trigger asChild>
        <button
          className={cn(
            "border-input data-[placeholder]:text-muted-foreground [&_svg:not([class*='text-'])]:text-muted-foreground focus-visible:border-ring focus-visible:ring-ring/50 aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive flex h-9 w-full items-center justify-between rounded-md border bg-transparent px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] outline-none focus-visible:ring-[3px] disabled:cursor-not-allowed disabled:opacity-50",
            className
          )}
          disabled={disabled}
          type="button"
        >
          <span className="flex-1 text-left truncate">
            {selectedOptions.length === 0 ? (
              <span className="text-muted-foreground">{placeholder}</span>
            ) : (
              displayText
            )}
          </span>
          <div className="flex items-center gap-2">
            {selectedOptions.length > 0 && (
              <button
                type="button"
                onClick={(e) => {
                  e.stopPropagation()
                  handleClearAll()
                }}
                className="hover:bg-muted rounded-sm p-0.5"
              >
                <XIcon className="h-3 w-3" />
              </button>
            )}
            <ChevronDownIcon className="h-4 w-4 opacity-50" />
          </div>
        </button>
      </DropdownMenuPrimitive.Trigger>

      <DropdownMenuPrimitive.Portal>
        <DropdownMenuPrimitive.Content
          className={cn(
            "bg-popover text-popover-foreground data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 z-50 max-h-60 w-[var(--radix-dropdown-menu-trigger-width)] min-w-[8rem] overflow-hidden rounded-md border shadow-md p-1"
          )}
          sideOffset={5}
          align="start"
        >
          {options.map((option) => {
            const isSelected = value.includes(option.value)
            return (
              <DropdownMenuPrimitive.Item
                key={option.value}
                className={cn(
                  "hover:bg-accent hover:text-accent-foreground relative flex cursor-pointer items-center gap-2 rounded-sm py-1.5 pl-2 pr-8 text-sm outline-none select-none data-[disabled]:pointer-events-none data-[disabled]:opacity-50",
                  option.disabled && "pointer-events-none opacity-50"
                )}
                onSelect={(e) => {
                  e.preventDefault()
                  if (!option.disabled) {
                    handleSelect(option.value)
                  }
                }}
              >
                <div className="flex items-center gap-2">
                  <CheckboxPrimitive.Root
                    checked={isSelected}
                    className={cn(
                      "border-primary focus-visible:ring-ring data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground peer h-4 w-4 shrink-0 rounded-sm border focus-visible:outline-none focus-visible:ring-1 disabled:cursor-not-allowed disabled:opacity-50"
                    )}
                  >
                    <CheckboxPrimitive.Indicator className="flex items-center justify-center text-current">
                      <CheckIcon className="h-3 w-3" />
                    </CheckboxPrimitive.Indicator>
                  </CheckboxPrimitive.Root>
                  <span>{option.label}</span>
                </div>
              </DropdownMenuPrimitive.Item>
            )
          })}
        </DropdownMenuPrimitive.Content>
      </DropdownMenuPrimitive.Portal>
    </DropdownMenuPrimitive.Root>
  )
}

// Alternative compact version with badges
export function MultiSelectWithBadges({
  options,
  value = [],
  onValueChange,
  placeholder = "Select options...",
  className,
  disabled = false,
}: MultiSelectProps) {
  const [open, setOpen] = React.useState(false)

  const handleSelect = (optionValue: string) => {
    const newValue = value.includes(optionValue)
      ? value.filter((v) => v !== optionValue)
      : [...value, optionValue]
    
    onValueChange?.(newValue)
  }

  const handleRemove = (optionValue: string) => {
    const newValue = value.filter((v) => v !== optionValue)
    onValueChange?.(newValue)
  }

  const selectedOptions = options.filter((option) => value.includes(option.value))

  return (
    <div className="space-y-2">
      <DropdownMenuPrimitive.Root open={open} onOpenChange={setOpen}>
        <DropdownMenuPrimitive.Trigger asChild>
          <button
            className={cn(
              "border-input data-[placeholder]:text-muted-foreground [&_svg:not([class*='text-'])]:text-muted-foreground focus-visible:border-ring focus-visible:ring-ring/50 aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive flex h-9 w-full items-center justify-between rounded-md border bg-transparent px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] outline-none focus-visible:ring-[3px] disabled:cursor-not-allowed disabled:opacity-50",
              className
            )}
            disabled={disabled}
            type="button"
          >
            <span className="flex-1 text-left">
              {selectedOptions.length === 0 ? (
                <span className="text-muted-foreground">{placeholder}</span>
              ) : (
                `${selectedOptions.length} selected`
              )}
            </span>
            <ChevronDownIcon className="h-4 w-4 opacity-50" />
          </button>
        </DropdownMenuPrimitive.Trigger>

        <DropdownMenuPrimitive.Portal>
          <DropdownMenuPrimitive.Content
            className={cn(
              "bg-popover text-popover-foreground data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2 z-50 max-h-60 w-[var(--radix-dropdown-menu-trigger-width)] min-w-[8rem] overflow-hidden rounded-md border shadow-md p-1"
            )}
            sideOffset={5}
            align="start"
          >
            {options.map((option) => {
              const isSelected = value.includes(option.value)
              return (
                <DropdownMenuPrimitive.Item
                  key={option.value}
                  className={cn(
                    "hover:bg-accent hover:text-accent-foreground relative flex cursor-pointer items-center gap-2 rounded-sm py-1.5 pl-2 pr-8 text-sm outline-none select-none data-[disabled]:pointer-events-none data-[disabled]:opacity-50",
                    option.disabled && "pointer-events-none opacity-50"
                  )}
                  onSelect={(e) => {
                    e.preventDefault()
                    if (!option.disabled) {
                      handleSelect(option.value)
                    }
                  }}
                >
                  <div className="flex items-center gap-2">
                    <CheckboxPrimitive.Root
                      checked={isSelected}
                      className={cn(
                        "border-primary focus-visible:ring-ring data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground peer h-4 w-4 shrink-0 rounded-sm border focus-visible:outline-none focus-visible:ring-1 disabled:cursor-not-allowed disabled:opacity-50"
                      )}
                    >
                      <CheckboxPrimitive.Indicator className="flex items-center justify-center text-current">
                        <CheckIcon className="h-3 w-3" />
                      </CheckboxPrimitive.Indicator>
                    </CheckboxPrimitive.Root>
                    <span>{option.label}</span>
                  </div>
                </DropdownMenuPrimitive.Item>
              )
            })}
          </DropdownMenuPrimitive.Content>
        </DropdownMenuPrimitive.Portal>
      </DropdownMenuPrimitive.Root>

      {/* Selected badges */}
      {selectedOptions.length > 0 && (
        <div className="flex flex-wrap gap-1">
          {selectedOptions.map((option) => (
            <span
              key={option.value}
              className="bg-primary/10 text-primary hover:bg-primary/20 inline-flex items-center gap-1 rounded-md px-2 py-1 text-xs font-medium"
            >
              {option.label}
              <button
                type="button"
                onClick={() => handleRemove(option.value)}
                className="hover:bg-primary/30 rounded-full p-0.5"
              >
                <XIcon className="h-3 w-3" />
              </button>
            </span>
          ))}
        </div>
      )}
    </div>
  )
}