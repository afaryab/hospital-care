import{c as k,a as E,j as r}from"./utils-q4EKThuO.js";import{B as j}from"./badge-DghL8o_I.js";import{C as V,a as y}from"./card-tJerkyCy.js";import{C as N}from"./currency-CQP0FS4i.js";import{c as _,d as b}from"./index-CSrV_1Wb.js";import"./iframe-DOYQQv_f.js";import"./preload-helper-PPVm8Dsz.js";import"./index-BZoqsebz.js";const v=C=>{const e=k.c(23),{voucher:s,className:h,onClick:d}=C,f=d&&"cursor-pointer hover:shadow-md transition-shadow";let t;e[0]!==h||e[1]!==f?(t=E("cursor-default",f,h),e[0]=h,e[1]=f,e[2]=t):t=e[2];let a;e[3]!==s.vc_number?(a=r.jsx("p",{className:"text-xs font-mono text-muted-foreground",children:s.vc_number}),e[3]=s.vc_number,e[4]=a):a=e[4];let o;e[5]!==s.amount?(o=r.jsx("p",{className:"font-semibold text-sm mt-0.5",children:r.jsx(N,{value:s.amount})}),e[5]=s.amount,e[6]=o):o=e[6];let c;e[7]!==s.payed_to_name?(c=s.payed_to_name&&r.jsx("p",{className:"text-xs text-muted-foreground truncate",children:s.payed_to_name}),e[7]=s.payed_to_name,e[8]=c):c=e[8];let n;e[9]!==a||e[10]!==o||e[11]!==c?(n=r.jsxs("div",{className:"min-w-0",children:[a,o,c]}),e[9]=a,e[10]=o,e[11]=c,e[12]=n):n=e[12];const g=s.status==="payed"?"default":"secondary";let m;e[13]!==g||e[14]!==s.status?(m=r.jsx(j,{variant:g,className:"text-xs shrink-0 capitalize",children:s.status}),e[13]=g,e[14]=s.status,e[15]=m):m=e[15];let i;e[16]!==n||e[17]!==m?(i=r.jsxs(V,{className:"p-4 flex items-center justify-between gap-3",children:[n,m]}),e[16]=n,e[17]=m,e[18]=i):i=e[18];let l;return e[19]!==d||e[20]!==t||e[21]!==i?(l=r.jsx(y,{className:t,onClick:d,children:i}),e[19]=d,e[20]=t,e[21]=i,e[22]=l):l=e[22],l};v.__docgenInfo={description:"",methods:[],displayName:"ExpenseVoucherCard"};const $={title:"Elements/ExpenseVoucher/ExpenseVoucherCard",component:v,tags:["autodocs"],parameters:{layout:"centered"}},u={args:{voucher:_}},p={args:{voucher:b}},x={args:{voucher:_,onClick:()=>alert("Voucher clicked")}};u.parameters={...u.parameters,docs:{...u.parameters?.docs,source:{originalSource:`{
  args: {
    voucher: mockExpenseVoucher
  }
}`,...u.parameters?.docs?.source}}};p.parameters={...p.parameters,docs:{...p.parameters?.docs,source:{originalSource:`{
  args: {
    voucher: mockExpenseVoucherPending
  }
}`,...p.parameters?.docs?.source}}};x.parameters={...x.parameters,docs:{...x.parameters?.docs,source:{originalSource:`{
  args: {
    voucher: mockExpenseVoucher,
    onClick: () => alert('Voucher clicked')
  }
}`,...x.parameters?.docs?.source}}};const q=["Paid","Pending","Clickable"];export{x as Clickable,u as Paid,p as Pending,q as __namedExportsOrder,$ as default};
