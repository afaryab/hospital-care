import{c as h,a as T,j as a}from"./utils-q4EKThuO.js";import{B as j}from"./badge-DghL8o_I.js";import{C as E,a as N}from"./card-tJerkyCy.js";import{C as b}from"./currency-CQP0FS4i.js";import{p as _,q as v}from"./index-CSrV_1Wb.js";import"./iframe-DOYQQv_f.js";import"./preload-helper-PPVm8Dsz.js";import"./index-BZoqsebz.js";const g=k=>{const e=h.c(20),{transaction:s,className:x,onClick:m}=k,f=m&&"cursor-pointer hover:shadow-md transition-shadow";let t;e[0]!==x||e[1]!==f?(t=T("cursor-default",f,x),e[0]=x,e[1]=f,e[2]=t):t=e[2];let r;e[3]!==s.tr_number?(r=a.jsx("p",{className:"text-xs font-mono text-muted-foreground",children:s.tr_number}),e[3]=s.tr_number,e[4]=r):r=e[4];let n;e[5]!==s.amount?(n=a.jsx("p",{className:"font-semibold text-sm mt-0.5",children:a.jsx(b,{value:s.amount})}),e[5]=s.amount,e[6]=n):n=e[6];let o;e[7]!==r||e[8]!==n?(o=a.jsxs("div",{className:"min-w-0",children:[r,n]}),e[7]=r,e[8]=n,e[9]=o):o=e[9];const C=s.income_or_expense==="INCOME"?"default":"destructive";let c;e[10]!==C||e[11]!==s.income_or_expense?(c=a.jsx(j,{variant:C,className:"text-xs shrink-0",children:s.income_or_expense}),e[10]=C,e[11]=s.income_or_expense,e[12]=c):c=e[12];let i;e[13]!==o||e[14]!==c?(i=a.jsxs(E,{className:"p-4 flex items-center justify-between gap-3",children:[o,c]}),e[13]=o,e[14]=c,e[15]=i):i=e[15];let l;return e[16]!==m||e[17]!==t||e[18]!==i?(l=a.jsx(N,{className:t,onClick:m,children:i}),e[16]=m,e[17]=t,e[18]=i,e[19]=l):l=e[19],l};g.__docgenInfo={description:"",methods:[],displayName:"TransactionCard"};const M={title:"Elements/Transaction/TransactionCard",component:g,tags:["autodocs"],parameters:{layout:"centered"}},p={args:{transaction:_}},d={args:{transaction:v}},u={args:{transaction:_,onClick:()=>alert("Transaction clicked")}};p.parameters={...p.parameters,docs:{...p.parameters?.docs,source:{originalSource:`{
  args: {
    transaction: mockTransaction
  }
}`,...p.parameters?.docs?.source}}};d.parameters={...d.parameters,docs:{...d.parameters?.docs,source:{originalSource:`{
  args: {
    transaction: mockTransactionExpense
  }
}`,...d.parameters?.docs?.source}}};u.parameters={...u.parameters,docs:{...u.parameters?.docs,source:{originalSource:`{
  args: {
    transaction: mockTransaction,
    onClick: () => alert('Transaction clicked')
  }
}`,...u.parameters?.docs?.source}}};const $=["Income","Expense","Clickable"];export{u as Clickable,d as Expense,p as Income,$ as __namedExportsOrder,M as default};
