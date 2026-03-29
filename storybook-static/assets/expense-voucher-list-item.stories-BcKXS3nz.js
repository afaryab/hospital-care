import{c as P,a as I,j as r}from"./utils-q4EKThuO.js";import{B as S}from"./badge-DghL8o_I.js";import{C as w}from"./currency-CQP0FS4i.js";import{c as j,d as C}from"./index-CSrV_1Wb.js";import"./iframe-DOYQQv_f.js";import"./preload-helper-PPVm8Dsz.js";import"./index-BZoqsebz.js";const b=N=>{const e=P.c(29),{voucher:s,className:f,onClick:t,selected:V}=N,g=V&&"bg-accent",v=t&&"cursor-pointer";let o;e[0]!==f||e[1]!==g||e[2]!==v?(o=I("flex items-center justify-between gap-3 px-3 py-2 rounded-md","hover:bg-accent transition-colors",g,v,f),e[0]=f,e[1]=g,e[2]=v,e[3]=o):o=e[3];const _=t?"button":void 0,E=t?0:void 0;let a;e[4]!==t?(a=t?k=>k.key==="Enter"&&t():void 0,e[4]=t,e[5]=a):a=e[5];let n;e[6]!==s.vc_number?(n=r.jsx("p",{className:"text-xs font-mono text-muted-foreground",children:s.vc_number}),e[6]=s.vc_number,e[7]=n):n=e[7];let c;e[8]!==s.amount?(c=r.jsx("span",{className:"text-sm font-semibold",children:r.jsx(w,{value:s.amount})}),e[8]=s.amount,e[9]=c):c=e[9];let m;e[10]!==s.payed_to_name?(m=s.payed_to_name&&r.jsxs("span",{className:"text-xs text-muted-foreground truncate",children:["-> ",s.payed_to_name]}),e[10]=s.payed_to_name,e[11]=m):m=e[11];let i;e[12]!==c||e[13]!==m?(i=r.jsxs("div",{className:"flex items-baseline gap-1.5",children:[c,m]}),e[12]=c,e[13]=m,e[14]=i):i=e[14];let d;e[15]!==i||e[16]!==n?(d=r.jsxs("div",{className:"min-w-0",children:[n,i]}),e[15]=i,e[16]=n,e[17]=d):d=e[17];const y=s.status==="payed"?"default":"secondary";let l;e[18]!==y||e[19]!==s.status?(l=r.jsx(S,{variant:y,className:"text-xs shrink-0 capitalize",children:s.status}),e[18]=y,e[19]=s.status,e[20]=l):l=e[20];let u;return e[21]!==t||e[22]!==d||e[23]!==l||e[24]!==o||e[25]!==_||e[26]!==E||e[27]!==a?(u=r.jsxs("div",{className:o,onClick:t,role:_,tabIndex:E,onKeyDown:a,children:[d,l]}),e[21]=t,e[22]=d,e[23]=l,e[24]=o,e[25]=_,e[26]=E,e[27]=a,e[28]=u):u=e[28],u};b.__docgenInfo={description:"",methods:[],displayName:"ExpenseVoucherListItem"};const $={title:"Elements/ExpenseVoucher/ExpenseVoucherListItem",component:b,tags:["autodocs"],parameters:{layout:"padded"}},p={args:{voucher:j}},x={args:{voucher:C}},h={args:{voucher:j,selected:!0}};p.parameters={...p.parameters,docs:{...p.parameters?.docs,source:{originalSource:`{
  args: {
    voucher: mockExpenseVoucher
  }
}`,...p.parameters?.docs?.source}}};x.parameters={...x.parameters,docs:{...x.parameters?.docs,source:{originalSource:`{
  args: {
    voucher: mockExpenseVoucherPending
  }
}`,...x.parameters?.docs?.source}}};h.parameters={...h.parameters,docs:{...h.parameters?.docs,source:{originalSource:`{
  args: {
    voucher: mockExpenseVoucher,
    selected: true
  }
}`,...h.parameters?.docs?.source}}};const q=["Paid","Pending","Selected"];export{p as Paid,x as Pending,h as Selected,q as __namedExportsOrder,$ as default};
