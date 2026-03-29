import{c as h,a as j,j as t}from"./utils-q4EKThuO.js";import{B as N}from"./badge-DghL8o_I.js";import{C as P,a as y}from"./card-tJerkyCy.js";import{C as _}from"./currency-CQP0FS4i.js";import{i as C,j as w}from"./index-CSrV_1Wb.js";import"./iframe-DOYQQv_f.js";import"./preload-helper-PPVm8Dsz.js";import"./index-BZoqsebz.js";const k=R=>{const e=h.c(23),{receaveable:a,className:f,onClick:m}=R,x=m&&"cursor-pointer hover:shadow-md transition-shadow";let s;e[0]!==f||e[1]!==x?(s=j("cursor-default",x,f),e[0]=f,e[1]=x,e[2]=s):s=e[2];const v=a.transaction?.tr_number;let r;e[3]!==v?(r=t.jsx("p",{className:"text-xs font-mono text-muted-foreground",children:v}),e[3]=v,e[4]=r):r=e[4];let c;e[5]!==a.amount?(c=t.jsx("p",{className:"font-semibold text-sm mt-0.5",children:t.jsx(_,{value:a.amount})}),e[5]=a.amount,e[6]=c):c=e[6];let o;e[7]!==a.patient?(o=a.patient?.name&&t.jsx("p",{className:"text-xs text-muted-foreground truncate",children:a.patient.name}),e[7]=a.patient,e[8]=o):o=e[8];let n;e[9]!==r||e[10]!==c||e[11]!==o?(n=t.jsxs("div",{className:"min-w-0",children:[r,c,o]}),e[9]=r,e[10]=c,e[11]=o,e[12]=n):n=e[12];const g=a.status==="paid"?"default":"secondary";let l;e[13]!==a.status||e[14]!==g?(l=t.jsx(N,{variant:g,className:"text-xs shrink-0",children:a.status}),e[13]=a.status,e[14]=g,e[15]=l):l=e[15];let i;e[16]!==n||e[17]!==l?(i=t.jsxs(P,{className:"p-4 flex items-center justify-between gap-3",children:[n,l]}),e[16]=n,e[17]=l,e[18]=i):i=e[18];let d;return e[19]!==m||e[20]!==i||e[21]!==s?(d=t.jsx(y,{className:s,onClick:m,children:i}),e[19]=m,e[20]=i,e[21]=s,e[22]=d):d=e[22],d};k.__docgenInfo={description:"",methods:[],displayName:"ReceaveableCard"};const A={title:"Elements/Receivable/ReceaveableCard",component:k,tags:["autodocs"],parameters:{layout:"centered"}},p={args:{receaveable:C}},u={args:{receaveable:w}},b={args:{receaveable:C,onClick:()=>alert("Receivable clicked")}};p.parameters={...p.parameters,docs:{...p.parameters?.docs,source:{originalSource:`{
  args: {
    receaveable: mockReceaveable
  }
}`,...p.parameters?.docs?.source}}};u.parameters={...u.parameters,docs:{...u.parameters?.docs,source:{originalSource:`{
  args: {
    receaveable: mockReceaveablePaid
  }
}`,...u.parameters?.docs?.source}}};b.parameters={...b.parameters,docs:{...b.parameters?.docs,source:{originalSource:`{
  args: {
    receaveable: mockReceaveable,
    onClick: () => alert('Receivable clicked')
  }
}`,...b.parameters?.docs?.source}}};const D=["Pending","Paid","Clickable"];export{b as Clickable,u as Paid,p as Pending,D as __namedExportsOrder,A as default};
